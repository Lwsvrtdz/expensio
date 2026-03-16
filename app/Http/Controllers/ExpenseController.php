<?php

namespace App\Http\Controllers;

use App\Enums\GroupMemberStatus;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Services\ExpenseSplitService;
use App\Models\Expense;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseSplitService $splitter,
    ) {}

    public function storeForGroup(StoreExpenseRequest $request, Group $group): RedirectResponse
    {
        $actor = $this->actor();

        $this->assertActorIsAcceptedMember($actor, $group);

        $expense = Expense::query()->create([
            'group_id' => $group->id,
            'paid_by' => $actor->id,
            'description' => $request->string('description')->toString(),
            'amount' => $request->float('amount'),
        ]);

        $this->handleSplits($request, $expense, $group, $actor);

        return back()->with('success', 'Expense added.');
    }

    public function storePersonal(StoreExpenseRequest $request): RedirectResponse
    {
        $actor = $this->actor();

        $expense = Expense::query()->create([
            'group_id' => null,
            'paid_by' => $actor->id,
            'description' => $request->string('description')->toString(),
            'amount' => $request->float('amount'),
        ]);

        $this->handleSplits($request, $expense, null, $actor);

        return back()->with('success', 'Expense added.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $actor = $this->actor();

        abort_unless($expense->paid_by === $actor->id, 403);

        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }

    private function handleSplits(
        StoreExpenseRequest $request,
        Expense $expense,
        ?Group $group,
        User $actor,
    ): void {
        /** @var array<int, array{user_id?: int|null, member_email?: string|null, amount?: float|int}>|null $splits */
        $splits = $request->input('splits');

        if (is_array($splits) && $splits !== []) {
            foreach ($splits as $split) {
                $expense->splits()->create([
                    'user_id' => $split['user_id'] ?? null,
                    'member_email' => $split['member_email'] ?? null,
                    'amount' => (float) ($split['amount'] ?? 0),
                    'settled' => false,
                ]);
            }

            return;
        }

        if ($group !== null) {
            $members = GroupMember::query()
                ->where('group_id', $group->id)
                //->where('status', GroupMemberStatus::Accepted->value)
                ->get(['user_id', 'invite_email']);

            $recipients = [];

            foreach ($members as $member) {
                $recipients[] = [
                    'user_id' => $member->user_id,
                    'member_email' => $member->invite_email,
                ];
            }

            $this->splitter->splitEvenly($expense, $recipients);

            return;
        }

        $this->splitter->splitEvenly($expense, [
            [
                'user_id' => $actor->id,
                'member_email' => null,
            ],
        ]);
    }

    private function assertActorIsAcceptedMember(User $actor, Group $group): void
    {
        $isMember = GroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $actor->id)
            ->where('status', GroupMemberStatus::Accepted->value)
            ->exists();

        abort_unless($isMember, 403);
    }
}

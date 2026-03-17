<?php

namespace App\Http\Services;

use App\Enums\GroupMemberStatus;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function __construct(
        private readonly ExpenseSplitService $splitter,
    ) {}

    public function storeForGroup(StoreExpenseRequest $request, Group $group, User $actor): Expense
    {
        return DB::transaction(function () use ($request, $group, $actor): Expense {
            [$paidBy, $payerEmail] = $this->resolveGroupPayer(
                $group,
                $this->extractPayerKey($request),
                $actor,
            );

            $expense = $this->createExpenseRecord(
                $request,
                $group,
                $paidBy,
                $payerEmail,
            );

            $this->persistSplits($request, $expense, $group, $actor);

            return $expense;
        });
    }

    public function storePersonal(StoreExpenseRequest $request, User $actor): Expense
    {
        return DB::transaction(function () use ($request, $actor): Expense {
            $expense = $this->createExpenseRecord(
                $request,
                null,
                $actor->id,
                null,
            );

            $this->persistSplits($request, $expense, null, $actor);

            return $expense;
        });
    }

    private function createExpenseRecord(
        StoreExpenseRequest $request,
        ?Group $group,
        ?int $paidBy,
        ?string $payerEmail,
    ): Expense {
        return Expense::query()->create([
            'group_id' => $group?->id,
            'paid_by' => $paidBy,
            'payer_email' => $payerEmail,
            'description' => $request->string('description')->toString(),
            'amount' => $request->float('amount'),
        ]);
    }

    private function persistSplits(
        StoreExpenseRequest $request,
        Expense $expense,
        ?Group $group,
        User $actor,
    ): void {
        /** @var array<int, array{user_id?: int|null, member_email?: string|null, amount?: float|int}>|null $splits */
        $splits = $request->input('splits');

        if (is_array($splits) && $splits !== []) {
            $this->persistManualSplits($expense, $splits);

            return;
        }

        $this->splitter->splitEvenly(
            $expense,
            $this->buildAutoSplitRecipients($group, $actor),
        );
    }

    /**
     * @param array<int, array{user_id?: int|null, member_email?: string|null, amount?: float|int}> $splits
     */
    private function persistManualSplits(Expense $expense, array $splits): void
    {
        foreach ($splits as $split) {
            $expense->splits()->create([
                'user_id' => $split['user_id'] ?? null,
                'member_email' => $split['member_email'] ?? null,
                'amount' => (float) ($split['amount'] ?? 0),
                'settled' => false,
            ]);
        }
    }

    /**
     * @return array<int, array{user_id?: int|null, member_email?: string|null}>
     */
    private function buildAutoSplitRecipients(?Group $group, User $actor): array
    {
        if ($group === null) {
            return [
                [
                    'user_id' => $actor->id,
                    'member_email' => null,
                ],
            ];
        }

        return GroupMember::query()
            ->where('group_id', $group->id)
            ->get(['user_id', 'invite_email'])
            ->map(fn(GroupMember $member): array => [
                'user_id' => $member->user_id,
                'member_email' => $member->invite_email,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{0: int|null, 1: string|null}
     */
    private function resolveGroupPayer(Group $group, ?string $payerKey, User $actor): array
    {
        if ($payerKey === null || $payerKey === '') {
            return [$actor->id, null];
        }

        [$kind, $value] = array_pad(explode(':', $payerKey, 2), 2, null);

        if ($kind === 'user' && $value !== null) {
            $userId = (int) $value;

            $isMember = $group->members()
                ->where('user_id', $userId)
                ->exists();

            if ($isMember) {
                return [$userId, null];
            }
        }

        if ($kind === 'email' && $value !== null) {
            $isMember = $group->members()
                ->where('invite_email', $value)
                ->exists();

            if ($isMember) {
                return [null, $value];
            }
        }

        return [$actor->id, null];
    }

    private function extractPayerKey(StoreExpenseRequest $request): ?string
    {
        /** @var string|null $payerKey */
        $payerKey = $request->input('payer_key');

        if ($payerKey === null || $payerKey === '') {
            /** @var string|null $payerKey */
            $payerKey = $request->input('payerKey');
        }

        return $payerKey;
    }
}

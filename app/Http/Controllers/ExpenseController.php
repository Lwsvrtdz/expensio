<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Services\ExpenseService;
use App\Models\Expense;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenses,
    ) {}

    public function storeForGroup(StoreExpenseRequest $request, Group $group): RedirectResponse
    {
        $actor = $this->actor();

        $this->authorize('create', [Expense::class, $group]);

        $this->expenses->storeForGroup($request, $group, $actor);

        return back()->with('success', 'Expense added.');
    }

    public function storePersonal(StoreExpenseRequest $request): RedirectResponse
    {
        $this->expenses->storePersonal($request, $this->actor());

        return back()->with('success', 'Expense added.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return back()->with('success', 'Expense deleted.');
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorize('update', $expense);

        $this->expenses->updateExpense($request, $expense, $this->actor());

        return back()->with('success', 'Expense updated.');
    }
}

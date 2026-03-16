<?php

namespace App\Http\Services;

use App\Models\Expense;
use App\Models\ExpenseSplit;

class ExpenseSplitService
{
    /**
     * @param array<int, array{user_id?: int|null, member_email?: string|null}> $recipients
     */
    public function splitEvenly(Expense $expense, array $recipients): void
    {
        $count = count($recipients);

        if ($count === 0) {
            return;
        }

        $totalCents = (int) round($expense->amount * 100);

        $baseShare = intdiv($totalCents, $count);
        $remainder = $totalCents - ($baseShare * $count);

        foreach ($recipients as $index => $recipient) {
            $shareCents = $baseShare + ($index < $remainder ? 1 : 0);

            ExpenseSplit::query()->create([
                'expense_id' => $expense->id,
                'user_id' => $recipient['user_id'] ?? null,
                'member_email' => $recipient['member_email'] ?? null,
                'amount' => $shareCents / 100,
                'settled' => false,
            ]);
        }
    }
}


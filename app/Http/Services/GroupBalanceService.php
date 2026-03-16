<?php

namespace App\Http\Services;

use App\Models\Group;
use App\Models\User;

class GroupBalanceService
{
    /**
     * @return array<int, array{
     *     from: array{key: string, label: string, type: string},
     *     to: array{key: string, label: string, type: string},
     *     amount: float
     * }>
     */
    public function forGroup(Group $group): array
    {
        /** @var array<string, array<string, int>> $debts */
        $debts = [];

        /** @var array<string, array{label: string, type: string}> $participants */
        $participants = [];

        foreach ($group->expenses as $expense) {
            /** @var User|null $payer */
            $payer = $expense->payer;

            if (! $payer) {
                continue;
            }

            $payerKey = 'user:'.$payer->id;
            $participants[$payerKey] = [
                'label' => $payer->name ?? $payer->email,
                'type' => 'user',
            ];

            foreach ($expense->splits as $split) {
                $debtorKey = null;
                $debtorLabel = null;
                $debtorType = null;

                if ($split->user) {
                    $debtorKey = 'user:'.$split->user->id;
                    $debtorLabel = $split->user->name ?? $split->user->email;
                    $debtorType = 'user';
                } elseif ($split->member_email) {
                    $debtorKey = 'email:'.$split->member_email;
                    $debtorLabel = $split->member_email;
                    $debtorType = 'email';
                }

                if ($debtorKey === null || $debtorKey === $payerKey) {
                    continue;
                }

                $participants[$debtorKey] = [
                    'label' => $debtorLabel,
                    'type' => $debtorType,
                ];

                $amountCents = (int) round($split->amount * 100);

                if (! isset($debts[$debtorKey])) {
                    $debts[$debtorKey] = [];
                }

                if (! isset($debts[$debtorKey][$payerKey])) {
                    $debts[$debtorKey][$payerKey] = 0;
                }

                $debts[$debtorKey][$payerKey] += $amountCents;
            }
        }

        /** @var array<string, array{a: string, b: string, amount: int}> $netPairs */
        $netPairs = [];

        foreach ($debts as $debtorKey => $creditors) {
            foreach ($creditors as $creditorKey => $amountCents) {
                if ($debtorKey === $creditorKey) {
                    continue;
                }

                $a = $debtorKey < $creditorKey ? $debtorKey : $creditorKey;
                $b = $debtorKey < $creditorKey ? $creditorKey : $debtorKey;
                $pairKey = $a.'|'.$b;

                if (! isset($netPairs[$pairKey])) {
                    $netPairs[$pairKey] = [
                        'a' => $a,
                        'b' => $b,
                        'amount' => 0,
                    ];
                }

                if ($debtorKey === $a) {
                    $netPairs[$pairKey]['amount'] += $amountCents;
                } else {
                    $netPairs[$pairKey]['amount'] -= $amountCents;
                }
            }
        }

        $results = [];

        foreach ($netPairs as $pair) {
            if ($pair['amount'] === 0) {
                continue;
            }

            $fromKey = $pair['amount'] > 0 ? $pair['a'] : $pair['b'];
            $toKey = $pair['amount'] > 0 ? $pair['b'] : $pair['a'];
            $amount = abs($pair['amount']) / 100;

            if (! isset($participants[$fromKey], $participants[$toKey])) {
                continue;
            }

            $results[] = [
                'from' => [
                    'key' => $fromKey,
                    'label' => $participants[$fromKey]['label'],
                    'type' => $participants[$fromKey]['type'],
                ],
                'to' => [
                    'key' => $toKey,
                    'label' => $participants[$toKey]['label'],
                    'type' => $participants[$toKey]['type'],
                ],
                'amount' => $amount,
            ];
        }

        return $results;
    }
}


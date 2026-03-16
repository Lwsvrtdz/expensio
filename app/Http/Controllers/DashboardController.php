<?php

namespace App\Http\Controllers;

use App\Http\Services\GroupBalanceService;
use App\Http\Services\GroupService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GroupService $groups,
        private readonly GroupBalanceService $balances,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $groups = $this->groups->index($user);

        $authKey = 'user:' . $user->id;
        $totalOwed = 0.0;
        $totalOwes = 0.0;

        foreach ($groups as $group) {
            $balances = $this->balances->forGroup($group);

            foreach ($balances as $balance) {
                if ($balance['to']['key'] === $authKey) {
                    $totalOwed += $balance['amount'];
                } elseif ($balance['from']['key'] === $authKey) {
                    $totalOwes += $balance['amount'];
                }
            }
        }

        return Inertia::render('Dashboard', [
            'summary' => [
                'total_owed' => $totalOwed,
                'total_owes' => $totalOwes,
            ],
            'groups' => $groups,
        ]);
    }
}

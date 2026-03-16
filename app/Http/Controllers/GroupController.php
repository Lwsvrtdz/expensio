<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Services\GroupBalanceService;
use App\Http\Services\GroupService;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GroupController extends Controller
{
    public function __construct(
        private readonly GroupService $groups,
        private readonly GroupBalanceService $balances,
    ) {}

    public function index(Request $request): Response
    {
        $groups = $this->groups->index($request->user());

        return Inertia::render('Groups/Index', [
            'groups' => $groups,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Groups/Create', [
            'group' => new Group(),
        ]);
    }

    public function store(StoreGroupRequest $request): RedirectResponse
    {
        $group = $this->groups->store($request->user(), $request);

        return to_route('groups.show', $group);
    }

    public function show(Request $request, Group $group): Response
    {
        $group = $this->groups->show($request->user(), $group);
        $balances = $this->balances->forGroup($group);

        return Inertia::render('Groups/Show', [
            'group' => $group,
            'balances' => $balances,
        ]);
    }

    public function destroy(Request $request, Group $group): RedirectResponse
    {
        $this->groups->destroy($request->user(), $group);

        return to_route('groups.index');
    }
}

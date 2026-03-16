<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteMemberRequest;
use App\Http\Services\GroupMemberService;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;

class GroupMemberController extends Controller
{
    public function __construct(
        private readonly GroupMemberService $groupMembers,
    ) {}

    public function store(InviteMemberRequest $request, Group $group): RedirectResponse
    {
        $this->groupMembers->store($request->user(), $group, $request);

        return back()->with('success', 'Member invited successfully.');
    }
}

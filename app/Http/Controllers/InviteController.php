<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Services\InviteService;
use App\Models\GroupMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function __construct(
        private readonly InviteService $invites,
    ) {}

    public function show(Request $request, string $token): View|RedirectResponse
    {
        /** @var GroupMember|null $member */
        $member = GroupMember::query()
            ->with('group')
            ->where('invite_token', $token)
            ->first();

        if (! $member || ! $member->group) {
            throw CustomException::internalException();
        }

        if (! $request->user()) {
            $request->session()->put('pending_invite_token', $token);

            return redirect()->route('register');
        }

        return view('invites.show', [
            'group' => $member->group,
            'member' => $member,
            'token' => $token,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $user = $this->actor();

        $member = $this->invites->acceptForUser($user, $token);

        $request->session()->forget('pending_invite_token');

        return redirect()->route('groups.show', $member->group_id);
    }
}

<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Http\Services\UserParticipantLinkService;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        if (session()->has('pending_invite_token')) {
            app(\App\Http\Services\InviteService::class)
                ->acceptForUser($user, (string) session('pending_invite_token'));

            session()->forget('pending_invite_token');
        }

        app(UserParticipantLinkService::class)
            ->linkForUserEmail($user, $user->email);

        return $user;
    }
}

<?php

namespace App\Http\Requests;

use App\Models\GroupMember;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InviteMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $group = $this->route('group');

            if (! $group) {
                return;
            }

            $email = $this->input('email');

            $exists = GroupMember::query()
                ->where('group_id', $group->id)
                ->where(function ($query) use ($email) {
                    $query->where('invite_email', $email)
                        ->orWhereHas('user', function ($sub) use ($email) {
                            $sub->where('email', $email);
                        });
                })
                ->exists();

            if ($exists) {
                $validator->errors()->add('email', 'This email is already a member of the group.');
            }
        });
    }
}

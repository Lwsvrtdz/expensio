<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payer_key' => ['nullable', 'string'],
            'splits' => ['nullable', 'array'],
            'splits.*.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'splits.*.member_email' => ['nullable', 'email'],
            'splits.*.member_key' => ['nullable', 'string'],
            'splits.*.amount' => ['required_with:splits', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var \App\Models\Expense|null $expense */
            $expense = $this->route('expense');
            $group = $expense?->group;

            if ($group !== null) {
                /** @var string|null $payerKey */
                $payerKey = $this->input('payer_key');

                if ($payerKey === null || $payerKey === '') {
                    /** @var string|null $payerKey */
                    $payerKey = $this->input('payerKey');
                }

                if ($payerKey === null || $payerKey === '') {
                    $validator->errors()->add(
                        'payer_key',
                        'Please select who paid for this expense.',
                    );
                } else {
                    [$kind, $value] = array_pad(explode(':', $payerKey, 2), 2, null);

                    $isValidPayer = false;

                    if ($kind === 'user' && $value !== null) {
                        $isValidPayer = $group->members()
                            ->where('user_id', (int) $value)
                            ->exists();
                    } elseif ($kind === 'email' && $value !== null) {
                        $isValidPayer = $group->members()
                            ->where('invite_email', $value)
                            ->exists();
                    }

                    if (! $isValidPayer) {
                        $validator->errors()->add(
                            'payer_key',
                            'The selected payer must be a member of this group.',
                        );
                    }
                }
            }

            /** @var array<int, array{amount?: float|int}>|null $splits */
            $splits = $this->input('splits');

            if (! is_array($splits) || $splits === []) {
                return;
            }

            $total = array_reduce(
                $splits,
                /**
                 * @param  array{amount?: float|int}  $item
                 */
                fn (float $carry, array $item): float => $carry + (float) ($item['amount'] ?? 0),
                0.0,
            );

            $expected = (float) $this->input('amount', 0);

            if (abs($total - $expected) > 0.01) {
                $validator->errors()->add(
                    'splits',
                    'The total of all splits must equal the expense amount.',
                );
            }
        });
    }
}

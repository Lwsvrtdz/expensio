<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreExpenseRequest extends FormRequest
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
            'splits' => ['nullable', 'array'],
            'splits.*.user_id' => ['nullable', 'integer', 'exists:users,id'],
            'splits.*.member_email' => ['nullable', 'email'],
            'splits.*.amount' => ['required_with:splits', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var array<int, array{amount?: float|int}>|null $splits */
            $splits = $this->input('splits');

            if (! is_array($splits) || $splits === []) {
                return;
            }

            $total = array_reduce(
                $splits,
                /**
                 * @param float $carry
                 * @param array{amount?: float|int} $item
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


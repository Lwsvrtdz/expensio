<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSplit extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'expense_id',
        'user_id',
        'member_email',
        'amount',
        'settled',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'settled' => 'boolean',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

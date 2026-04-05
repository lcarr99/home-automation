<?php

namespace App\Models;

use App\Domain\Budgeting\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
        ];
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}

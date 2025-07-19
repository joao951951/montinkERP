<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'discount_value',
        'minimum_order',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_count',
        'active'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}
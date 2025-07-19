<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'active'
    ];

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class);
    }

    public function inventory(): hasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
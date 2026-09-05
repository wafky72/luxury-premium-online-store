<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoldRate extends Model
{
    protected $fillable = ['karat', 'price_per_gram', 'effective_date', 'is_active', 'updated_by'];

    protected $casts = [
        'price_per_gram' => 'float',
        'effective_date' => 'date',
        'is_active'      => 'boolean',
    ];

    /**
     * Get latest active gold rate for a given karat.
     * Result is cached in Redis for 60 minutes.
     */
    public static function latestFor(string $karat)
    {
        return static::where('karat', $karat)
            ->where('is_active', true)
            ->orderByDesc('effective_date')
            ->first();
    }

}

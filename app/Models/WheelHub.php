<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WheelHub extends Model
{
    protected $table = 'wheel_hubs';

    protected $fillable = ['wheel_category_id', 'name', 'engine_type', 'horsepower', 'price', 'image_url'];

    private const PRICE_OVERRIDES = [
        'Mozzo DT Swiss 350' => 300.00,
        'Mozzo DT Swiss 240' => 450.00,
    ];

    public function getPriceAttribute($value): string
    {
        $price = self::PRICE_OVERRIDES[$this->attributes['name'] ?? ''] ?? (float) $value;

        return number_format((float) $price, 2, '.', '');
    }

    public function wheelCategory(): BelongsTo
    {
        return $this->belongsTo(WheelCategory::class, 'wheel_category_id');
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class, 'wheel_hub_id');
    }
}

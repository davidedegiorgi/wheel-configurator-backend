<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WheelCategory extends Model
{
    protected $table = 'wheel_categories';

    protected $fillable = ['name', 'description', 'base_price', 'hero_image_url', 'available_colors'];

    protected function casts(): array
    {
        return [
            'available_colors' => 'array',
        ];
    }

    public function wheelHubs(): HasMany
    {
        return $this->hasMany(WheelHub::class, 'wheel_category_id');
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(Configuration::class, 'wheel_category_id');
    }
}

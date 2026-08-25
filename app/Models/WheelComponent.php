<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WheelComponent extends Model
{
    protected $table = 'wheel_components';

    protected $fillable = ['name', 'description', 'price', 'category', 'exclusive_group', 'image_url'];

    public function configurations(): BelongsToMany
    {
        return $this->belongsToMany(Configuration::class, 'configuration_component', 'component_id', 'configuration_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ComponentImage::class, 'component_id');
    }
}

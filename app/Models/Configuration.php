<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Configuration extends Model
{
    protected $fillable = ['user_id', 'wheel_category_id', 'wheel_hub_id', 'name', 'description', 'total_price'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wheelCategory(): BelongsTo
    {
        return $this->belongsTo(WheelCategory::class, 'wheel_category_id');
    }

    public function wheelHub(): BelongsTo
    {
        return $this->belongsTo(WheelHub::class, 'wheel_hub_id');
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(WheelComponent::class, 'configuration_component', 'configuration_id', 'component_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}

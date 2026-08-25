<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponentImage extends Model
{
    protected $table = 'component_images';

    protected $fillable = ['component_id', 'wheel_category_id', 'image_url'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(WheelComponent::class, 'component_id');
    }

    public function wheelCategory(): BelongsTo
    {
        return $this->belongsTo(WheelCategory::class, 'wheel_category_id');
    }
}

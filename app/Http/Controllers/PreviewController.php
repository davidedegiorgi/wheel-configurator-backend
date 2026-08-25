<?php

namespace App\Http\Controllers;

use App\Models\WheelCategory;
use App\Models\WheelHub;
use App\Models\WheelComponent;
use Illuminate\Http\Request;

class PreviewController extends Controller
{
    /**
     * Get preview data for a configuration
     * Returns image URL with metadata for rendering on frontend
     */
    public function configurationPreview(Request $request)
    {
        $validated = $request->validate([
            'wheel_category_id' => 'required|exists:wheel_categories,id',
            'wheel_hub_id' => 'nullable|exists:wheel_hubs,id',
        ]);

        $wheelCategory = WheelCategory::findOrFail($validated['wheel_category_id']);
        $wheelHub = ($validated['wheel_hub_id'] ?? null)
            ? WheelHub::findOrFail($validated['wheel_hub_id'])
            : null;

        $previewImage = $wheelHub?->image_url ?? $wheelCategory->hero_image_url;

        return response()->json([
            'preview_image' => $previewImage,
            'wheel_category' => [
                'id' => $wheelCategory->id,
                'name' => $wheelCategory->name,
                'description' => $wheelCategory->description,
                'base_price' => $wheelCategory->base_price,
            ],
            'wheel_hub' => $wheelHub ? [
                'id' => $wheelHub->id,
                'name' => $wheelHub->name,
                'horsepower' => $wheelHub->horsepower,
                'price' => $wheelHub->price,
            ] : null,
        ]);
    }

    /**
     * Get wheel category with all images
     */
    public function wheelCategoryWithImages(string $id)
    {
        $wheelCategory = WheelCategory::with('wheelHubs')->findOrFail($id);

        return response()->json([
            'wheel_category' => $wheelCategory,
            'wheel_hubs' => $wheelCategory->wheelHubs,
            'components' => WheelComponent::all(),
        ]);
    }
}

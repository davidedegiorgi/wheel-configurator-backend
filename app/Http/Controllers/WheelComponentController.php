<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWheelComponentRequest;
use App\Models\WheelComponent;

class WheelComponentController extends Controller
{
    public function index()
    {
        $components = WheelComponent::with('images')->get();
        return response()->json($components);
    }

    public function grouped()
    {
        $components = WheelComponent::with('images')->get();
        
        $grouped = [];
        foreach ($components as $optional) {
            $category = $optional->category;
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $optional;
        }
        
        return response()->json($grouped);
    }

    public function store(StoreWheelComponentRequest $request)
    {
        $optional = WheelComponent::create($request->validated());
        return response()->json($optional, 201);
    }

    public function show(string $id)
    {
        $optional = WheelComponent::with('images')->findOrFail($id);
        return response()->json($optional);
    }

    public function update(StoreWheelComponentRequest $request, string $id)
    {
        $optional = WheelComponent::findOrFail($id);
        $optional->update($request->validated());
        return response()->json($optional);
    }

    public function destroy(string $id)
    {
        WheelComponent::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}

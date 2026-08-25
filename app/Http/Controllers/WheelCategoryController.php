<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWheelCategoryRequest;
use App\Models\WheelCategory;

class WheelCategoryController extends Controller
{
    public function index()
    {
        $wheelCategorys = WheelCategory::with('wheelHubs')->get();
        return response()->json($wheelCategorys);
    }

    public function store(StoreWheelCategoryRequest $request)
    {
        $wheelCategory = WheelCategory::create($request->validated());
        return response()->json($wheelCategory, 201);
    }

    public function show(string $id)
    {
        $wheelCategory = WheelCategory::with('wheelHubs')->findOrFail($id);
        return response()->json($wheelCategory);
    }

    public function update(StoreWheelCategoryRequest $request, string $id)
    {
        $wheelCategory = WheelCategory::findOrFail($id);
        $wheelCategory->update($request->validated());
        return response()->json($wheelCategory);
    }

    public function destroy(string $id)
    {
        WheelCategory::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}


<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWheelHubRequest;
use App\Models\WheelHub;

class WheelHubController extends Controller
{
    public function index()
    {
        $wheelHubs = WheelHub::all();
        return response()->json($wheelHubs);
    }

    public function store(StoreWheelHubRequest $request)
    {
        $wheelHub = WheelHub::create($request->validated());
        return response()->json($wheelHub, 201);
    }

    public function show(string $id)
    {
        $wheelHub = WheelHub::findOrFail($id);
        return response()->json($wheelHub);
    }

    public function update(StoreWheelHubRequest $request, string $id)
    {
        $wheelHub = WheelHub::findOrFail($id);
        $wheelHub->update($request->validated());
        return response()->json($wheelHub);
    }

    public function destroy(string $id)
    {
        WheelHub::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}


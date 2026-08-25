<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigurationRequest;
use App\Http\Requests\UpdateConfigurationRequest;
use App\Models\Configuration;
use App\Services\ConfiguratorService;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    public function __construct(
        protected ConfiguratorService $configuratorService
    ) {}

    public function index()
    {
        $configurations = Configuration::where('user_id', Auth::id())
            ->with(['wheelCategory', 'wheelHub', 'components'])
            ->get();
        return response()->json($configurations);
    }

    public function store(StoreConfigurationRequest $request)
    {
        // Verifica compatibilita tra linea ruote e set mozzi
        if (!$this->configuratorService->checkCompatibility(
            $request->wheel_category_id,
            $request->wheel_hub_id
        )) {
            return response()->json([
                'message' => 'Set mozzi non compatibile con la linea ruote selezionata'
            ], 422);
        }

        // Verifica componenti obbligatori e compatibilità degli optional
        $validated = $request->validated();
        $componentIds = $validated['components'] ?? $validated['component_ids'] ?? [];

        $requiredCheck = $this->configuratorService->checkRequiredComponents($componentIds);
        if (!$requiredCheck['valid']) {
            return response()->json([
                'message' => $requiredCheck['message'],
                'missing' => $requiredCheck['missing'] ?? [],
            ], 422);
        }

        $compatibilityCheck = $this->configuratorService->checkComponentCompatibility($componentIds, $request->wheel_hub_id);
        if (!$compatibilityCheck['valid']) {
            return response()->json([
                'message' => $compatibilityCheck['message'],
                'group' => $compatibilityCheck['group'] ?? null,
            ], 422);
        }

        $configuration = $this->configuratorService->createConfiguration(
            $validated,
            Auth::id()
        );

        return response()->json($configuration->load(['wheelCategory', 'wheelHub', 'components']), 201);
    }

    public function show(string $id)
    {
        $configuration = Configuration::where('user_id', Auth::id())
            ->with(['wheelCategory', 'wheelHub', 'components'])
            ->findOrFail($id);

        return response()->json($configuration);
    }

    public function update(UpdateConfigurationRequest $request, string $id)
    {
        $configuration = Configuration::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validated();

        // Verifica compatibilità dei componenti se forniti
        if ($request->has('components') || $request->has('component_ids')) {
            $componentIds = $validated['components'] ?? $validated['component_ids'] ?? [];
            $requiredCheck = $this->configuratorService->checkRequiredComponents($componentIds);
            if (!$requiredCheck['valid']) {
                return response()->json([
                    'message' => $requiredCheck['message'],
                    'missing' => $requiredCheck['missing'] ?? [],
                ], 422);
            }

            $compatibilityCheck = $this->configuratorService->checkComponentCompatibility(
                $componentIds,
                $validated['wheel_hub_id'] ?? $configuration->wheel_hub_id
            );
            if (!$compatibilityCheck['valid']) {
                return response()->json([
                    'message' => $compatibilityCheck['message'],
                    'group' => $compatibilityCheck['group'] ?? null,
                ], 422);
            }
        }

        $configuration = $this->configuratorService->updateConfiguration(
            $configuration,
            $validated
        );

        return response()->json($configuration->load(['wheelCategory', 'wheelHub', 'components']));
    }

    public function destroy(string $id)
    {
        $configuration = Configuration::where('user_id', Auth::id())->findOrFail($id);
        $configuration->delete();
        return response()->json(null, 204);
    }
}

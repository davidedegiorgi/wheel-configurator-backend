<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\WheelComponent;

class ConfiguratorService
{
    /**
     * Calcola il prezzo totale della configurazione
     */
    public function calculateTotalPrice(Configuration $configuration): float
    {
        $basePrice = $configuration->wheelCategory->base_price;
        $wheelHubPrice = $configuration->wheelHub->price;
        $componentsPrice = $this->calculateComponentsPrice($configuration);

        $subtotal = $basePrice + $wheelHubPrice + $componentsPrice;

        return $subtotal * 1.12;
    }

    private function calculateComponentsPrice(Configuration $configuration): float
    {
        return $configuration->components()->get()->sum(function (WheelComponent $component) use ($configuration) {
            return $this->getComponentEffectivePrice($component, $configuration);
        });
    }

    private function getComponentEffectivePrice(WheelComponent $component, Configuration $configuration): float
    {
        $price = (float) $component->price;

        return $this->isSpokeComponent($component)
            ? $price * $this->getSpokeCount($configuration)
            : $price;
    }

    private function getSpokeCount(Configuration $configuration): int
    {
        return str_contains(strtolower($configuration->wheelCategory->name ?? ''), 'mtb') ? 56 : 48;
    }

    private function isSpokeComponent(WheelComponent $component): bool
    {
        $name = strtolower($component->name);

        return $component->exclusive_group === 'spoke'
            || str_contains($name, 'sapim')
            || str_contains($name, 'raggi')
            || str_contains($name, 'berd');
    }

    /**
     * Crea una configurazione con componenti
     */
    public function createConfiguration(array $data, int $userId): Configuration
    {
        $data['user_id'] = $userId;

        // Normalize component keys: accept either 'components' or 'component_ids'
        if (!isset($data['components']) && isset($data['component_ids'])) {
            $data['components'] = $data['component_ids'];
        }

        // Ensure total_price is set to a default to avoid DB not-null constraint
        if (!isset($data['total_price'])) {
            $data['total_price'] = 0;
        }
        // Calcola il prezzo totale temporaneamente
        $configuration = new Configuration($data);
        $configuration->save();

        // Aggancia i componenti selezionati
        if (isset($data['components']) && is_array($data['components'])) {
            $configuration->components()->sync($data['components']);
        }

        // Aggiorna il prezzo totale
        $totalPrice = $this->calculateTotalPrice($configuration);
        $configuration->update(['total_price' => $totalPrice]);

        return $configuration;
    }

    /**
     * Verifica compatibilita tra componenti ruote
     */
    public function checkCompatibility(int $wheelCategoryId, int $wheelHubId): bool
    {
        $wheelHub = \App\Models\WheelHub::find($wheelHubId);

        if (!$wheelHub) {
            return false;
        }

        return $wheelHub->wheel_category_id === $wheelCategoryId;
    }

    public function checkRequiredComponents(array $componentIds): array
    {
        $components = WheelComponent::whereIn('id', $componentIds)->get();

        $hasProfile = $components->contains(function (WheelComponent $component) {
            $name = strtolower($component->name);
            $category = strtolower($component->category);

            return $category === 'profilo' || str_contains($name, 'profilo');
        });

        $hasSpokes = $components->contains(fn (WheelComponent $component) => $this->isSpokeComponent($component));

        $missing = [];
        if (!$hasProfile) {
            $missing[] = 'profilo';
        }
        if (!$hasSpokes) {
            $missing[] = 'raggi';
        }

        if ($missing) {
            return [
                'valid' => false,
                'message' => 'Completa tutti i componenti prima di salvare: ' . implode(', ', $missing),
                'missing' => $missing,
            ];
        }

        return ['valid' => true];
    }


    /**
     * Verifica compatibilità dei componenti
     */
    public function checkComponentCompatibility(array $componentIds, ?int $wheelHubId = null): array
    {
        if (empty($componentIds)) {
            return ['valid' => true];
        }

        $components = WheelComponent::whereIn('id', $componentIds)->get();
        $wheelHub = $wheelHubId ? \App\Models\WheelHub::find($wheelHubId) : null;

        // Raggruppa per exclusive_group
        $groups = [];
        foreach ($components as $component) {
            if ($component->exclusive_group) {
                if (!isset($groups[$component->exclusive_group])) {
                    $groups[$component->exclusive_group] = [];
                }
                $groups[$component->exclusive_group][] = $component;
            }
        }

        // Verifica che ogni gruppo abbia al massimo 1 elemento
        foreach ($groups as $groupName => $groupItems) {
            if (count($groupItems) > 1) {
                $names = array_map(fn($o) => $o->name, $groupItems);
                return [
                    'valid' => false,
                    'message' => "Non è possibile selezionare più di un componente per questa categoria: " . implode(', ', $names),
                    'group' => $groupName,
                ];
            }
        }

        foreach ($components as $component) {
            $componentName = strtolower($component->name);
            $hubName = strtolower($wheelHub->name ?? '');

            if (str_contains($componentName, 'berd') &&
                !str_contains($hubName, 'extralight') &&
                !str_contains($hubName, 'erase')) {
                return [
                    'valid' => false,
                    'message' => 'Raggi Berd compatibili solo con mozzi Extralight o Erase',
                    'group' => 'spoke',
                ];
            }

            if (str_contains($componentName, 'carbon') && !str_contains($hubName, 'bitex')) {
                return [
                    'valid' => false,
                    'message' => 'Raggi Carbon compatibili solo con mozzo Bitex',
                    'group' => 'spoke',
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Aggiorna una configurazione
     */
    public function updateConfiguration(Configuration $configuration, array $data): Configuration
    {
        if (!isset($data['components']) && isset($data['component_ids'])) {
            $data['components'] = $data['component_ids'];
        }

        $configuration->update([
            'name' => $data['name'] ?? $configuration->name,
            'description' => $data['description'] ?? $configuration->description,
            'wheel_hub_id' => $data['wheel_hub_id'] ?? $configuration->wheel_hub_id,
        ]);

        // Aggiorna componenti se forniti
        if (isset($data['components']) && is_array($data['components'])) {
            $configuration->components()->sync($data['components']);
        }

        // Ricalcola il prezzo totale
        $totalPrice = $this->calculateTotalPrice($configuration);
        $configuration->update(['total_price' => $totalPrice]);
        $configuration->quotes()
            ->where('status', '!=', 'exported')
            ->update(['total_amount' => $totalPrice]);

        return $configuration;
    }

    /**
     * Genera il dettaglio del preventivo
     */
    public function generateQuoteDetails(Configuration $configuration): array
    {
        return [
            'wheel_category' => [
                'name' => $configuration->wheelCategory->name,
                'price' => $configuration->wheelCategory->base_price, 
            ],
            'wheel_hub' => [
                'name' => $configuration->wheelHub->name,
                'engine_type' => $configuration->wheelHub->engine_type,
                'horsepower' => $configuration->wheelHub->horsepower,
                'price' => $configuration->wheelHub->price,
            ],
            'components' => $configuration->components()->get()->map(function (WheelComponent $component) use ($configuration) {
                $spokeCount = $this->getSpokeCount($configuration);

                return [
                    'id' => $component->id,
                    'name' => $component->name,
                    'category' => $component->category,
                    'price' => $this->getComponentEffectivePrice($component, $configuration),
                    'unit_price' => $component->price,
                    'spoke_count' => $this->isSpokeComponent($component) ? $spokeCount : null,
                ];
            })->toArray(),
            'total' => $configuration->total_price,
        ];
    }
}

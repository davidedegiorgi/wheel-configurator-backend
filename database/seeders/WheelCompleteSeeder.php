<?php

namespace Database\Seeders;

use App\Models\WheelCategory;
use App\Models\WheelHub;
use App\Models\WheelComponent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WheelCompleteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('quotes')->delete();
        DB::table('configuration_component')->delete();
        DB::table('configurations')->delete();
        DB::table('component_images')->delete();
        WheelComponent::query()->delete();
        WheelHub::query()->delete();
        WheelCategory::query()->delete();

        $models = [
            [
                'key' => 'road',
                'name' => 'Strada',
                'description' => null,
                'base_price' => 0,
                'hero_image_url' => '/wheel-configurator/cards/strada.jpg',
            ],
            [
                'key' => 'gravel',
                'name' => 'Gravel',
                'description' => null,
                'base_price' => 0,
                'hero_image_url' => '/wheel-configurator/cards/gravel.jpg',
            ],
            [
                'key' => 'mtb',
                'name' => 'MTB',
                'description' => null,
                'base_price' => 0,
                'hero_image_url' => '/wheel-configurator/cards/mtb.jpg',
            ],
        ];

        $createdModels = [];
        foreach ($models as $model) {
            $createdModels[$model['key']] = WheelCategory::create([
                'name' => $model['name'],
                'description' => $model['description'],
                'base_price' => $model['base_price'],
                'hero_image_url' => $model['hero_image_url'],
                'available_colors' => null,
            ]);
        }

        $hubOptions = [
            ['Mozzo DT Swiss 350', 'mozzo', 0, 300, '/wheel-configurator/components/mozzo-dtswiss350.jpg'],
            ['Mozzo DT Swiss 240', 'mozzo', 0, 450, '/wheel-configurator/components/mozzo-dtswiss240.jpg'],
            ['Mozzo DT Swiss 180', 'mozzo', 0, 700, '/wheel-configurator/components/mozzo-dtswiss180.jpg'],
            ['Mozzo Extralight', 'mozzo', 0, 700, '/wheel-configurator/components/mozzo-extralight.jpeg'],
            ['Mozzo Damil', 'mozzo', 0, 300, '/wheel-configurator/components/mozzo-damil.webp'],
            ['Mozzo Erase', 'mozzo', 0, 330, '/wheel-configurator/components/mozzo-erase.jpg'],
            ['Mozzo Bitex', 'mozzo', 0, 250, '/wheel-configurator/components/mozzo-bitex.png'],
            ['Mozzo Industry Nine', 'mozzo', 0, 700, '/wheel-configurator/components/mozzo-industrynine.webp'],
            ['Mozzo Chris King', 'mozzo', 0, 850, '/wheel-configurator/components/mozzo-chrisking.webp'],
            ['Mozzo OGS', 'mozzo', 0, 350, '/wheel-configurator/components/mozzo-ogs.webp'],
            ['Mozzo Spank', 'mozzo', 0, 370, '/wheel-configurator/components/mozzo-spank.webp'],
        ];

        $wheel_hubs = array_fill_keys(array_keys($createdModels), $hubOptions);

        foreach ($wheel_hubs as $modelKey => $items) {
            foreach ($items as [$name, $type, $weight, $price, $imageUrl]) {
                WheelHub::create([
                    'wheel_category_id' => $createdModels[$modelKey]->id,
                    'name' => $name,
                    'engine_type' => $type,
                    'horsepower' => $weight,
                    'price' => $price,
                    'image_url' => $imageUrl,
                ]);
            }
        }

        foreach ($this->wheel_components() as $optional) {
            WheelComponent::create($optional);
        }
    }

    private function wheel_components(): array
    {
        return [
            ['name' => 'Profilo 20 mm', 'description' => null, 'price' => 800, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],
            ['name' => 'Profilo 30 mm', 'description' => null, 'price' => 760, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],
            ['name' => 'Profilo 45 mm', 'description' => null, 'price' => 800, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],
            ['name' => 'Profilo 60 mm', 'description' => null, 'price' => 800, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],
            ['name' => 'Profilo 45/50mm wave', 'description' => null, 'price' => 800, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],
            ['name' => 'Profilo 40 mm', 'description' => null, 'price' => 800, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],
            ['name' => 'Profilo 35/40mm', 'description' => null, 'price' => 800, 'category' => 'profilo', 'exclusive_group' => 'profile', 'image_url' => null],

            ['name' => 'Sapim CX-Ray', 'description' => null, 'price' => 3, 'category' => 'raggi', 'exclusive_group' => 'spoke', 'image_url' => null],
            ['name' => 'Sapim Laser', 'description' => null, 'price' => 1.60, 'category' => 'raggi', 'exclusive_group' => 'spoke', 'image_url' => null],
            ['name' => 'Sapim Sprint', 'description' => null, 'price' => 1.60, 'category' => 'raggi', 'exclusive_group' => 'spoke', 'image_url' => null],
            ['name' => 'Sapim Leader', 'description' => null, 'price' => 1, 'category' => 'raggi', 'exclusive_group' => 'spoke', 'image_url' => null],
            ['name' => 'Raggi Carbon', 'description' => null, 'price' => 10, 'category' => 'raggi', 'exclusive_group' => 'spoke', 'image_url' => '/wheel-configurator/components/raggio-carbon.webp'],
            ['name' => 'Raggi Berd', 'description' => null, 'price' => 10, 'category' => 'raggi', 'exclusive_group' => 'spoke', 'image_url' => '/wheel-configurator/components/raggio-berd.png'],
        ];
    }

}

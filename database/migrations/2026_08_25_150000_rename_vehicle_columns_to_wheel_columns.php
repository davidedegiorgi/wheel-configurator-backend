<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameColumnIfExists('wheel_hubs', 'car_model_id', 'wheel_category_id');
        $this->renameColumnIfExists('configurations', 'car_model_id', 'wheel_category_id');
        $this->renameColumnIfExists('configurations', 'motorization_id', 'wheel_hub_id');
        $this->renameColumnIfExists('configuration_component', 'optional_id', 'component_id');
        $this->renameColumnIfExists('component_images', 'optional_id', 'component_id');
        $this->renameColumnIfExists('component_images', 'car_model_id', 'wheel_category_id');
    }

    public function down(): void
    {
        $this->renameColumnIfExists('wheel_hubs', 'wheel_category_id', 'car_model_id');
        $this->renameColumnIfExists('configurations', 'wheel_category_id', 'car_model_id');
        $this->renameColumnIfExists('configurations', 'wheel_hub_id', 'motorization_id');
        $this->renameColumnIfExists('configuration_component', 'component_id', 'optional_id');
        $this->renameColumnIfExists('component_images', 'component_id', 'optional_id');
        $this->renameColumnIfExists('component_images', 'wheel_category_id', 'car_model_id');
    }

    private function renameColumnIfExists(string $table, string $from, string $to): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $from) || Schema::hasColumn($table, $to)) {
            return;
        }

        DB::statement(sprintf('ALTER TABLE %s RENAME COLUMN %s TO %s', $table, $from, $to));
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->renameIfNeeded('car_models', 'wheel_categories');
        $this->renameIfNeeded('motorizations', 'wheel_hubs');
        $this->renameIfNeeded('optionals', 'wheel_components');
        $this->renameIfNeeded('configuration_optional', 'configuration_component');
        $this->renameIfNeeded('optional_images', 'component_images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->renameIfNeeded('component_images', 'optional_images');
        $this->renameIfNeeded('configuration_component', 'configuration_optional');
        $this->renameIfNeeded('wheel_components', 'optionals');
        $this->renameIfNeeded('wheel_hubs', 'motorizations');
        $this->renameIfNeeded('wheel_categories', 'car_models');
    }

    private function renameIfNeeded(string $from, string $to): void
    {
        if (Schema::hasTable($from) && !Schema::hasTable($to)) {
            Schema::rename($from, $to);
        }
    }
};

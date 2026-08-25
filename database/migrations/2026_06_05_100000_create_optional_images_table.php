<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('component_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('wheel_components')->cascadeOnDelete();
            $table->foreignId('wheel_category_id')->constrained('wheel_categories')->cascadeOnDelete();
            $table->string('image_url');
            $table->timestamps();

            $table->unique(['component_id', 'wheel_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('component_images');
    }
};

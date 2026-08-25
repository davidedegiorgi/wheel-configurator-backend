<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wheel_categories', function (Blueprint $table) {
            $table->string('hero_image_url')->nullable()->after('base_price');
            $table->json('available_colors')->nullable()->after('hero_image_url'); // Array di colori con hex e immagini
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wheel_categories', function (Blueprint $table) {
            $table->dropColumn(['hero_image_url', 'available_colors']);
        });
    }
};

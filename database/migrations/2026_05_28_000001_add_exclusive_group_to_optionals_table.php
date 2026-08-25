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
        Schema::table('wheel_components', function (Blueprint $table) {
            // exclusive_group: null = no restriction, 'color' = only one color, 'interior' = only one interior, etc.
            $table->string('exclusive_group')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wheel_components', function (Blueprint $table) {
            $table->dropColumn('exclusive_group');
        });
    }
};

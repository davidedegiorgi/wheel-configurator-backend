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
            if (!Schema::hasColumn('wheel_components', 'image_url')) {
                $table->string('image_url')->nullable()->after('category');
            }

            if (!Schema::hasColumn('wheel_components', 'exclusive_group')) {
                $table->string('exclusive_group')->nullable()->after('image_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wheel_components', function (Blueprint $table) {
            if (Schema::hasColumn('wheel_components', 'image_url')) {
                $table->dropColumn('image_url');
            }
        });
    }
};

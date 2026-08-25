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
        Schema::create('wheel_hubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_category_id')->constrained('wheel_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('engine_type');
            $table->integer('horsepower');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheel_hubs');
    }
};

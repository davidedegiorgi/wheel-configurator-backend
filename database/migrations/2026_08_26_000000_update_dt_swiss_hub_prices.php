<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('wheel_hubs')
            ->where('name', 'Mozzo DT Swiss 350')
            ->update(['price' => 300]);

        DB::table('wheel_hubs')
            ->where('name', 'Mozzo DT Swiss 240')
            ->update(['price' => 450]);
    }

    public function down(): void
    {
        DB::table('wheel_hubs')
            ->where('name', 'Mozzo DT Swiss 350')
            ->update(['price' => 700]);

        DB::table('wheel_hubs')
            ->where('name', 'Mozzo DT Swiss 240')
            ->update(['price' => 700]);
    }
};

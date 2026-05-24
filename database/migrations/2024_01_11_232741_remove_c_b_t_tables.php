<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = DB::select("SHOW TABLES");
        $tables = array_map('current',$tables);

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table)
        {
            if (\Illuminate\Support\Str::startsWith($table, 'cbt_'))
                Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

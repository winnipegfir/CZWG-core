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
        $columns = [
            'used_connect',
            'subdivision_code',
            'subdivision_name',
            'division_name',
            'division_code',
            'region_code',
            'region_name',
            'rating_short',
            'rating_long',
            'rating_GRP',
        ];

        foreach ($columns as $column) {
            Schema::table('users', function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

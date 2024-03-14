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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('used_connect');
            $table->dropColumn('subdivision_code');
            $table->dropColumn('subdivision_name');
            $table->dropColumn('division_name');
            $table->dropColumn('division_code');
            $table->dropColumn('region_code');
            $table->dropColumn('region_name');
            $table->dropColumn('rating_short');
            $table->dropColumn('rating_long');
            $table->dropColumn('rating_GRP');
        });
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

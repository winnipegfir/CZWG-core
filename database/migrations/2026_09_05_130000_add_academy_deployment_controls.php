<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_info', function (Blueprint $table) {
            $table->boolean('academy_preview_mode')->default(true)->after('emailwebmaster');
            $table->boolean('academy_nav_enabled')->default(true)->after('academy_preview_mode');
            $table->boolean('academy_staff_access_enabled')->default(false)->after('academy_nav_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('core_info', function (Blueprint $table) {
            $table->dropColumn(['academy_preview_mode', 'academy_nav_enabled', 'academy_staff_access_enabled']);
        });
    }
};

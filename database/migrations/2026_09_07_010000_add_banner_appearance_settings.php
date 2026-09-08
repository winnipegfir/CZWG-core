<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('core_info', 'bannerEnabled')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->boolean('bannerEnabled')->default(true)->after('bannerLink');
            });
        }

        if (! Schema::hasColumn('core_info', 'bannerTheme')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->string('bannerTheme', 30)->default('winnipeg')->after('bannerEnabled');
            });
        }

        if (! Schema::hasColumn('core_info', 'bannerAnimation')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->string('bannerAnimation', 30)->default('gold_swoop')->after('bannerTheme');
            });
        }

        if (! Schema::hasColumn('core_info', 'bannerIcon')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->string('bannerIcon', 30)->default('bullhorn')->after('bannerAnimation');
            });
        }

        if (! Schema::hasColumn('core_info', 'bannerOpenNewTab')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->boolean('bannerOpenNewTab')->default(false)->after('bannerIcon');
            });
        }
    }

    public function down(): void
    {
        foreach (['bannerOpenNewTab', 'bannerIcon', 'bannerAnimation', 'bannerTheme', 'bannerEnabled'] as $column) {
            if (Schema::hasColumn('core_info', $column)) {
                Schema::table('core_info', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};

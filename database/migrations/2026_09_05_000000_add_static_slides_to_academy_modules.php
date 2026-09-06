<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_modules', function (Blueprint $table) {
            if (! Schema::hasColumn('academy_modules', 'slide_count')) {
                $table->unsignedSmallInteger('slide_count')->default(0)->after('google_slides_url');
            }
            if (! Schema::hasColumn('academy_modules', 'slide_asset_path')) {
                $table->string('slide_asset_path')->nullable()->after('slide_count');
            }
            if (! Schema::hasColumn('academy_modules', 'audio_url')) {
                $table->string('audio_url')->nullable()->after('slide_asset_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('academy_modules', function (Blueprint $table) {
            if (Schema::hasColumn('academy_modules', 'audio_url')) {
                $table->dropColumn('audio_url');
            }
            if (Schema::hasColumn('academy_modules', 'slide_asset_path')) {
                $table->dropColumn('slide_asset_path');
            }
            if (Schema::hasColumn('academy_modules', 'slide_count')) {
                $table->dropColumn('slide_count');
            }
        });
    }
};

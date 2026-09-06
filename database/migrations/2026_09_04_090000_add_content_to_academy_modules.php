<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_modules', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('google_slides_url');
        });
    }

    public function down(): void
    {
        Schema::table('academy_modules', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
};

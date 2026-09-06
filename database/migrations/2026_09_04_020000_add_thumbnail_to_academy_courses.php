<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_courses', function (Blueprint $table) {
            $table->text('thumbnail')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('academy_courses', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academy_launch_notices', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->unsignedTinyInteger('display_count')->default(0);
            $table->boolean('dismissed')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_launch_notices');
    }
};

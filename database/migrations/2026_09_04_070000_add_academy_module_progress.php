<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('academy_module_progress', function (Blueprint $table) { $table->id(); $table->unsignedInteger('user_id'); $table->foreignId('module_id')->constrained('academy_modules')->cascadeOnDelete(); $table->timestamp('viewed_at'); $table->timestamps(); $table->unique(['user_id','module_id']); }); }
    public function down(): void { Schema::dropIfExists('academy_module_progress'); }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->uuid('availability_group')->nullable()->index();
            $table->unsignedTinyInteger('seat')->default(1);
            $table->unsignedTinyInteger('capacity')->default(1);
        });
    }

    public function down(): void
    {
        // Removing seat identity while overlapping sessions exist is unsafe.
        if (\Illuminate\Support\Facades\DB::table('training_sessions')->where('capacity', 2)->exists()) {
            throw new RuntimeException('Resolve double-training availability before rolling back seat metadata.');
        }
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropIndex(['availability_group']);
            $table->dropColumn(['availability_group', 'seat', 'capacity']);
        });
    }
};

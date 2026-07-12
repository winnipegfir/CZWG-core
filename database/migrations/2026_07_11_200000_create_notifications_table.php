<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Some environments have a leftover `notifications` table from an
        // unrelated, never-wired-up feature (columns like user_id/content/link
        // instead of Laravel's polymorphic notifiable_type/notifiable_id).
        // It's unused (empty) wherever this has been checked, so replace it
        // rather than leaving the new notification system unable to write to it.
        if (Schema::hasTable('notifications') && !Schema::hasColumn('notifications', 'notifiable_type')) {
            Schema::drop('notifications');
        }

        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Table predates this migration on some environments — never drop it here.
    }
};

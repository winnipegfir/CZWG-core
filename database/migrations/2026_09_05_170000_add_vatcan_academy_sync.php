<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academy_enrollments', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->after('course_id');
            $table->boolean('active')->default(true)->after('source');
            $table->integer('source_rating_id')->nullable()->after('active');
            $table->timestamp('source_synced_at')->nullable()->after('source_rating_id');
        });

        Schema::create('academy_vatcan_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cid')->unique();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('rating_id')->nullable();
            $table->string('rating_label', 20)->nullable();
            $table->json('entitled_course_slugs')->nullable();
            $table->boolean('active_home_member')->default(true)->index();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('academy_vatcan_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('initiated_by')->nullable();
            $table->string('status', 20);
            $table->unsignedInteger('controllers_found')->default(0);
            $table->unsignedInteger('visitors_ignored')->default(0);
            $table->unsignedInteger('users_matched')->default(0);
            $table->unsignedInteger('pending_cids')->default(0);
            $table->unsignedInteger('enrollments_activated')->default(0);
            $table->unsignedInteger('enrollments_deactivated')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_vatcan_sync_runs');
        Schema::dropIfExists('academy_vatcan_members');

        Schema::table('academy_enrollments', function (Blueprint $table) {
            $table->dropColumn(['source', 'active', 'source_rating_id', 'source_synced_at']);
        });
    }
};

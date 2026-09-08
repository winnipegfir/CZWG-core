<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('academy_courses', 'visitor_only')) {
            Schema::table('academy_courses', function (Blueprint $table) {
                $table->boolean('visitor_only')->default(false)->after('default_enrollment')->index();
            });
        }

        if (! Schema::hasColumn('academy_vatcan_members', 'active_visitor_member')) {
            Schema::table('academy_vatcan_members', function (Blueprint $table) {
                $table->boolean('active_visitor_member')->default(false)->after('active_home_member')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('academy_vatcan_members', 'active_visitor_member')) {
            Schema::table('academy_vatcan_members', function (Blueprint $table) {
                $table->dropColumn('active_visitor_member');
            });
        }

        if (Schema::hasColumn('academy_courses', 'visitor_only')) {
            Schema::table('academy_courses', function (Blueprint $table) {
                $table->dropColumn('visitor_only');
            });
        }
    }
};

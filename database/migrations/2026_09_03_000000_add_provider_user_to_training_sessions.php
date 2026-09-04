<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->unsignedInteger('instructor_id')->nullable()->change();
            $table->unsignedInteger('provider_user_id')->nullable()->after('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->foreign('provider_user_id')->references('id')->on('users');
        });

        $instructorUsers = DB::table('instructors')->pluck('user_id', 'id');
        foreach ($instructorUsers as $instructorId => $userId) {
            DB::table('training_sessions')
                ->where('instructor_id', $instructorId)
                ->update(['provider_user_id' => $userId]);
        }
    }

    public function down(): void
    {
        DB::table('training_sessions')->whereNull('instructor_id')->delete();

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->dropForeign(['provider_user_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('provider_user_id');
        });

        Schema::table('training_sessions', function (Blueprint $table) {
            $table->unsignedInteger('instructor_id')->nullable(false)->change();
            $table->foreign('instructor_id')->references('id')->on('instructors');
        });
    }
};

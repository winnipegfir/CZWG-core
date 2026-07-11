<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('instructing_sessions');

        Schema::create('training_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('instructor_id');
            $table->unsignedInteger('student_id')->nullable();
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->foreign('student_id')->references('id')->on('students');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('open');
            $table->string('type')->nullable();
            $table->string('network_callsign')->nullable();
            $table->text('instructor_comments')->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sessions');

        Schema::create('instructing_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id')->nullable();
            $table->unsignedInteger('instructor_id')->nullable();
            $table->string('type')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('network_callsign')->nullable();
            $table->text('instructor_comments')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtModuleAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbt_module_assigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cbt_module_id')->unsigned();
            $table->foreign('cbt_module_id')->references('id')->on('cbt_modules');
            $table->integer('student_id')->unsigned();
            $table->foreign('student_id')->references('id')->on('students');
            $table->integer('instructor_id')->nullable()->unsigned();
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->integer('intro')->default(1);
            $table->integer('lesson1')->default(0);
            $table->integer('lesson2')->nullable();
            $table->integer('lesson3')->nullable();
            $table->integer('lesson4')->nullable();
            $table->integer('lesson5')->nullable();
            $table->integer('lesson6')->nullable();
            $table->integer('lesson7')->nullable();
            $table->integer('lesson8')->nullable();
            $table->integer('lesson9')->nullable();
            $table->integer('lesson10')->nullable();
            $table->integer('conclusion')->default(0);
            $table->dateTime('assigned_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cbt_module_assigns');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbt_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumText('name');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('cbt_exam_id')->unsigned()->nullable();
            $table->foreign('cbt_exam_id')->references('id')->on('cbt_exams');
            $table->integer('assignall')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cbt_modules');
//
    }
}

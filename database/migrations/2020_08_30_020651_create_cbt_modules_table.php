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
            $table->longText('description_html');
            $table->longText('content_html');
            $table->integer('cbt_exam_id')->unsigned()->nullable();
            $table->foreign('cbt_exam_id')->references('id')->on('cbt_exams');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->integer('updated_by')->unsigned();
            $table->foreign('updated_by')->references('id')->on('users');
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

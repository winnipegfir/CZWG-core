<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtModuleLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbt_module_lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cbt_modules_id')->unsigned();
            $table->foreign('cbt_modules_id')->references('id')->on('cbt_modules');
            $table->mediumText('lesson');
            $table->mediumText('name');
            $table->longText('content_html');
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
        Schema::dropIfExists('cbt_module_lessons');
    }
}

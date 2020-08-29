<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('section_id')->unsigned()->nullable();
            $table->foreign('section_id')->references('id')->on('policies_sections');
            $table->text('name');
            $table->text('details');
            $table->text('link');
            $table->integer('embed')->default(0);
            $table->integer('author')->unsigned();
            $table->foreign('author')->references('id')->on('users');
            $table->date('releaseDate');
            $table->timestamps();
            $table->integer('staff_only')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policies');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRosterTable extends Migration
{
    /**
     * Run the migrations1.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('full_name');
            $table->text('division');
            /*not_certified, certified, training, instructor*/
            $table->string('status')->default('not_certified');
            /*manually edited by staff, perhaps eventually check it automatically idfk*/
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->integer('del');
            $table->integer('gnd');
            $table->integer('twr');
            $table->integer('dep');
            $table->integer('app');
            $table->integer('ctr');
            $table->text('remarks');
        });
    }

    /**
     * Reverse the migrations1.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roster');
    }
}

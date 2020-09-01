<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
		$table->increments('id');
		$table->integer('user_id')->unsigned();
		$table->foreign('user_id')->references('id')->on('users');
		$table->tinyInteger('enable_beta_components')->default(0);
		$table->string('ui_mode')->default('default');
		$table->tinyInteger('enable_discord_notifications')->default(0);
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
        //
    }
}

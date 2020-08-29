<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ticket_id')->unique();
            $table->text('title');
            $table->text('message');
            $table->integer('status')->default(0);
            $table->dateTime('submission_time');
            $table->timestamps();
            $table->integer('staff_member_id')->unsigned();
            $table->foreign('staff_member_id')->references('id')->on('staff_member');
            $table->integer('staff_member_cid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}

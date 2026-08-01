<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_warnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('roster_member_id')->unsigned()->nullable();
            $table->foreign('roster_member_id')->references('id')->on('roster');
            // Snapshotted so this row still means something if the roster entry
            // is later removed (e.g. the controller was let go over this).
            $table->integer('cid');
            $table->text('member_name');
            $table->string('quarter_label');
            $table->date('quarter_start');
            $table->double('hours_logged', 8, 2);
            $table->double('hours_required', 8, 2);
            $table->boolean('warning_sent')->default(false);
            $table->dateTime('warning_sent_at')->nullable();
            $table->integer('warning_sent_by')->unsigned()->nullable();
            $table->foreign('warning_sent_by')->references('id')->on('users');
            $table->date('deadline')->nullable();
            $table->string('result')->default('pending');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('activity_warnings');
    }
}

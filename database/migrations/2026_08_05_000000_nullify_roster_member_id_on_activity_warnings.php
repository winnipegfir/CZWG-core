<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullifyRosterMemberIdOnActivityWarnings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_warnings', function (Blueprint $table) {
            $table->dropForeign(['roster_member_id']);
            $table->foreign('roster_member_id')->references('id')->on('roster')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_warnings', function (Blueprint $table) {
            $table->dropForeign(['roster_member_id']);
            $table->foreign('roster_member_id')->references('id')->on('roster');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutOfOfficeToStaffMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_member', function (Blueprint $table) {
            $table->date('out_until')->nullable();
            $table->integer('contact_staff_member_id')->unsigned()->nullable();
            $table->foreign('contact_staff_member_id')->references('id')->on('staff_member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_member', function (Blueprint $table) {
            $table->dropForeign(['contact_staff_member_id']);
            $table->dropColumn(['out_until', 'contact_staff_member_id']);
        });
    }
}

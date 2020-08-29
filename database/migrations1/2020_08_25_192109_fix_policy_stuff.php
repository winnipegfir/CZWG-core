<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPolicyStuff extends Migration
{
    /**
     * Run the migrations1.
     *
     * @return void
     */
    public function up()
    {
    }

    /**
     * Reverse the migrations1.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('policies', function(Blueprint $table) {
            $table->dropColumn('section_id');
        });
    }
}

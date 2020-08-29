<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BringTheSectionsBacc extends Migration
{
    /**
     * Run the migrations1.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('policies', function(Blueprint $table) {
            $table->integer('section_id')
                ->references('id')
                ->on('policies_sections')
                ->nullable()
                ->after('id');
        });
    }

    /**
     * Reverse the migrations1.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

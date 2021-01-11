<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignallToCbtModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cbt_modules', function (Blueprint $table) {
            $table->integer('cbt_exam_id')->unsigned()->nullable();
            $table->foreign('cbt_exam_id')->references('id')->on('cbt_exams');    
            $table->integer('assignall')
                    ->after('cbt_exam_id')
                    ->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cbt_modules', function (Blueprint $table) {
            //
        });
    }
}

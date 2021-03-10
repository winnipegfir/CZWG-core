<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserExamAssignallToCbtModules extends Migration
{
/**
* Run the migrations.
*
* @return void
*/
public function up()
{
Schema::table('cbt_modules', function (Blueprint $table) {
            $table->integer('cbt_exam_id')->nullable()->unsigned();
            $table->foreign('cbt_exam_id')->references('id')->on('cbt_exams');
            $table->integer('assignall')->default('0');
            
        });
}

/**
* Reverse the migrations.
*
* @return void
*/
public function down()
{
Schema::dropIfExists('roles');
}
}

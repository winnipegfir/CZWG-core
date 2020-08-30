<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCbtExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cbt_exam_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cbt_exam_id')->unsigned();
            $table->foreign('cbt_exam_id')->references('id')->on('cbt_exams');
            $table->mediumText('question');
            $table->mediumText('option1');
            $table->mediumText('option2');
            $table->mediumText('option3')->nullable();
            $table->mediumText('option4')->nullable();
            $table->integer('answer');
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
        Schema::dropIfExists('cbt_exam_questions');
    }
}

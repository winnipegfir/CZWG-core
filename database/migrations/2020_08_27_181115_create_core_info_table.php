<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_info', function (Blueprint $table) {
            $table->increments('id');
            $table->text('sys_name');
            $table->text('release');
            $table->text('sys_build');
            $table->text('copyright_year');
            $table->timestamps();
            $table->text('banner');
            $table->text('bannerMode');
            $table->text('bannerLink');
            $table->text('emailfirchief');
            $table->text('emaildepfirchief');
            $table->text('emailcinstructor');
            $table->text('emaileventc');
            $table->text('emailfacilitye');
            $table->text('emailwebmaster');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_info');
    }
}

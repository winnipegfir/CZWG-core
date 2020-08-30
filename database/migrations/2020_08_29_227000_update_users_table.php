<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations1.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedInteger('id')->unique();
            $table->string('fname');
            $table->string('lname');
            $table->string('email')->change();
            $table->integer('rating_id')->default(null)->nullable();
            $table->string('rating_short')->default(null)->nullable();
            $table->string('rating_long')->default(null)->nullable();
            $table->string('rating_GRP')->default(null)->nullable();
            $table->dateTime('reg_date')->default(null)->nullable();
            $table->string('region_code')->default(null)->nullable();
            $table->string('region_name')->default(null)->nullable();
            $table->string('division_code')->default(null)->nullable();
            $table->string('division_name')->default(null)->nullable();
            $table->string('subdivision_code')->default(null)->nullable();
            $table->string('subdivision_name')->default(null)->nullable();
            $table->unsignedInteger('permissions')->default(0);
            $table->integer('gdpr_subscribed_emails')->default(0);
            $table->boolean('deleted')->default(false);
            $table->integer('init')->default(0);
            $table->string('avatar')->default('/img/default-profile-img.jpg');
            $table->longText('bio')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->tinyInteger('display_cid_only')->default(0);
            $table->string('display_fname')->nullable();
            $table->tinyInteger('display_last_name')->default(1);
            $table->bigInteger('discord_user_id')->nullable();
            $table->bigInteger('discord_dm_channel_id')->nullable();
            $table->integer('avatar_mode')->default(0);
            $table->tinyInteger('used_connect')->default(0);
            $table->integer('visitor')->default(0);
        });
    }

    /**
     * Reverse the migrations1.
     *
     * @return void
     */
    public function down()
    {

    }
}

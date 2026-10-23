<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWelcomeEmailSettingsToCoreInfo extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('core_info', 'welcome_email_enabled')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->boolean('welcome_email_enabled')->default(true);
            });
        }

        if (! Schema::hasColumn('core_info', 'welcome_email_subject')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->string('welcome_email_subject', 180)->nullable();
            });
        }

        if (! Schema::hasColumn('core_info', 'welcome_email_body')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->text('welcome_email_body')->nullable();
            });
        }

        if (! Schema::hasColumn('core_info', 'welcome_email_signature')) {
            Schema::table('core_info', function (Blueprint $table) {
                $table->text('welcome_email_signature')->nullable();
            });
        }

        DB::table('core_info')->whereNull('welcome_email_subject')->update([
            'welcome_email_subject' => 'Welcome to Winnipeg, {first_name}!',
            'welcome_email_body' => "Welcome to the Winnipeg FIR, we're very excited that you're here!\n\nWelcome to Winnipeg! This is the home to all things Winnipeg - from controller files, to roster info, to training, contact info and more. Thanks for stopping by!",
            'welcome_email_signature' => "Nate Power\nWinnipeg FIR Chief (WPG1)",
        ]);
    }

    public function down()
    {
        foreach (['welcome_email_signature', 'welcome_email_body', 'welcome_email_subject', 'welcome_email_enabled'] as $column) {
            if (Schema::hasColumn('core_info', $column)) {
                Schema::table('core_info', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}

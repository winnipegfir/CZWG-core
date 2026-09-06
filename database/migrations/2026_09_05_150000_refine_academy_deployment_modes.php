<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('core_info', function (Blueprint $table) {
            $table->string('academy_access_mode', 20)->default('admin')->after('academy_staff_access_enabled');
            $table->boolean('academy_maintenance_mode')->default(false)->after('academy_access_mode');
        });

        $settings = DB::table('core_info')->where('id', 1)->first();
        if ($settings) {
            $mode = 'admin';
            if (! ($settings->academy_preview_mode ?? true)) {
                $mode = 'normal';
            } elseif ($settings->academy_staff_access_enabled ?? false) {
                $mode = 'staff';
            }

            DB::table('core_info')->where('id', 1)->update([
                'academy_access_mode' => $mode,
                'academy_maintenance_mode' => false,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('core_info', function (Blueprint $table) {
            $table->dropColumn(['academy_access_mode', 'academy_maintenance_mode']);
        });
    }
};

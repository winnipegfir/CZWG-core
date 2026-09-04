<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sweatbox_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('position', 16);
            $table->string('name');
            $table->string('description', 500);
            $table->text('file_url');
            $table->date('updated_on');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['position', 'sort_order']);
        });

        $now = now();
        DB::table('sweatbox_files')->insert([
            ['position' => 'CYWG_GND', 'name' => '02 - Sweatbox Loading Guide.pdf', 'description' => 'Instructions for loading and using the ground Sweatbox exercises.', 'updated_on' => '2026-09-02', 'sort_order' => 10, 'file_url' => '/downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/02 - Sweatbox Loading Guide.pdf', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_GND', 'name' => 'Ex S1G01 Instructor Script.pdf', 'description' => 'Instructor script for exercise S1G01.', 'updated_on' => '2026-09-02', 'sort_order' => 20, 'file_url' => '/downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G01 Instructor Script.pdf', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_GND', 'name' => 'Ex S1G01.txt', 'description' => 'Sweatbox scenario file for exercise S1G01.', 'updated_on' => '2026-09-02', 'sort_order' => 30, 'file_url' => '/downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G01.txt', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_GND', 'name' => 'Ex S1G02 Instructor Script.pdf', 'description' => 'Instructor script for exercise S1G02.', 'updated_on' => '2026-09-02', 'sort_order' => 40, 'file_url' => '/downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G02 Instructor Script.pdf', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_GND', 'name' => 'Ex S1G02.txt', 'description' => 'Sweatbox scenario file for exercise S1G02.', 'updated_on' => '2026-09-02', 'sort_order' => 50, 'file_url' => '/downloads/sweatbox/CYWG_GND/CZWG Sweatbox Courseware v2025.01/Ex S1G02.txt', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_TWR', 'name' => 'TWRTrainerSetup.exe', 'description' => 'Tower Trainer installation program for Windows.', 'updated_on' => '2026-09-03', 'sort_order' => 10, 'file_url' => '/downloads/sweatbox/CYWG_TWR/TWRTrainerSetup.exe', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_TWR', 'name' => 'cywg.apt', 'description' => 'CYWG airport file for Tower Trainer.', 'updated_on' => '2026-09-03', 'sort_order' => 20, 'file_url' => '/downloads/sweatbox/CYWG_TWR/TWRTrainer files/cywg.apt', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_TWR', 'name' => 'cywg_twr_1.air', 'description' => 'CYWG tower traffic scenario for Tower Trainer.', 'updated_on' => '2026-09-03', 'sort_order' => 30, 'file_url' => '/downloads/sweatbox/CYWG_TWR/TWRTrainer files/cywg_twr_1.air', 'created_at' => $now, 'updated_at' => $now],
            ['position' => 'CYWG_TML', 'name' => 'CYWG_TML.txt', 'description' => 'Sweatbox scenario file for CYWG Terminal training.', 'updated_on' => '2026-09-03', 'sort_order' => 10, 'file_url' => '/downloads/sweatbox/CYWG_TML/CYWG_TML.txt', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sweatbox_files');
    }
};

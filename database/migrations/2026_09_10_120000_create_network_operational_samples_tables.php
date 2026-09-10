<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_operational_airport_samples', function (Blueprint $table) {
            $table->id();
            $table->timestamp('sampled_at')->index();
            $table->string('airport', 4)->index();
            $table->unsignedSmallInteger('ground_aircraft')->default(0);
            $table->unsignedSmallInteger('airborne_aircraft')->default(0);
            $table->unsignedSmallInteger('controlled_ground_aircraft')->default(0);
            $table->unsignedSmallInteger('controlled_airborne_aircraft')->default(0);
            $table->unsignedSmallInteger('online_positions')->default(0);
            $table->unsignedSmallInteger('active_positions')->default(0);
            $table->timestamps();
            $table->unique(['sampled_at', 'airport'], 'network_ops_airport_sample_unique');
        });

        Schema::create('network_operational_position_samples', function (Blueprint $table) {
            $table->id();
            $table->timestamp('sampled_at')->index();
            $table->string('airport', 4)->index();
            $table->string('callsign', 32);
            $table->unsignedInteger('controller_cid')->nullable();
            $table->string('position_type', 8)->index();
            $table->unsignedSmallInteger('relevant_aircraft')->default(0);
            $table->timestamps();
            $table->unique(['sampled_at', 'airport', 'callsign'], 'network_ops_position_sample_unique');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('network_operational_position_samples');
        Schema::dropIfExists('network_operational_airport_samples');
    }
};

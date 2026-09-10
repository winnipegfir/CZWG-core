<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('network_operational_flights')) {
            Schema::create('network_operational_flights', function (Blueprint $table) {
                $table->id();
                $table->string('session_key', 96)->unique();
                $table->unsignedInteger('pilot_cid')->nullable()->index();
                $table->string('callsign', 16)->index();
                $table->string('departure', 4)->nullable()->index();
                $table->string('arrival', 4)->nullable()->index();
                $table->string('airport', 4)->index();
                // DATETIME is used for compatibility with the production MySQL
                // server's legacy TIMESTAMP default rules. Eloquent still casts
                // these fields to Carbon dates normally.
                $table->dateTime('first_seen_at')->index();
                $table->dateTime('last_seen_at')->index();
                $table->dateTime('completed_at')->nullable()->index();
                $table->dateTime('last_sampled_at')->nullable();
                $table->string('last_phase', 12)->nullable();
                $table->boolean('last_controlled')->default(false);
                $table->unsignedInteger('ground_seconds')->default(0);
                $table->unsignedInteger('airborne_seconds')->default(0);
                $table->unsignedInteger('controlled_ground_seconds')->default(0);
                $table->unsignedInteger('controlled_airborne_seconds')->default(0);
                $table->boolean('emergency_observed')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('network_operational_emergencies')) {
            Schema::create('network_operational_emergencies', function (Blueprint $table) {
                $table->id();
                $table->string('session_key', 96)->index();
                $table->unsignedInteger('pilot_cid')->nullable();
                $table->string('callsign', 16)->index();
                $table->string('airport', 4)->index();
                $table->string('squawk', 4);
                $table->dateTime('first_seen_at')->index();
                $table->dateTime('last_seen_at');
                $table->timestamps();
                $table->unique(['session_key', 'squawk'], 'network_ops_emergency_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('network_operational_emergencies');
        Schema::dropIfExists('network_operational_flights');
    }
};

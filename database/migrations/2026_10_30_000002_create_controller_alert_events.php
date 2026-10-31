<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('controller_alert_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 64)->unique();
            $table->unsignedInteger('cid');
            $table->string('callsign', 40);
            $table->string('classification', 16);
            $table->string('delivery_status', 24)->default('attempting');
            $table->timestamp('created_at');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('controller_alert_events');
    }
};

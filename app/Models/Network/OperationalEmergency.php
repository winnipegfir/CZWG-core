<?php

namespace App\Models\Network;

use Illuminate\Database\Eloquent\Model;

class OperationalEmergency extends Model
{
    protected $table = 'network_operational_emergencies';
    protected $guarded = [];
    protected $casts = ['first_seen_at' => 'datetime', 'last_seen_at' => 'datetime'];
}

<?php

namespace App\Models\Network;

use Illuminate\Database\Eloquent\Model;

class OperationalFlight extends Model
{
    protected $table = 'network_operational_flights';
    protected $guarded = [];
    protected $casts = [
        'first_seen_at' => 'datetime', 'last_seen_at' => 'datetime',
        'completed_at' => 'datetime', 'last_sampled_at' => 'datetime',
        'last_controlled' => 'boolean', 'emergency_observed' => 'boolean',
    ];
}

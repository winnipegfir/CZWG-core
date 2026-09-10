<?php

namespace App\Models\Network;

use Illuminate\Database\Eloquent\Model;

class OperationalAirportSample extends Model
{
    protected $table = 'network_operational_airport_samples';

    protected $guarded = [];

    protected $casts = ['sampled_at' => 'datetime'];
}

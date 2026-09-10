<?php

namespace App\Models\Network;

use Illuminate\Database\Eloquent\Model;

class OperationalPositionSample extends Model
{
    protected $table = 'network_operational_position_samples';

    protected $guarded = [];

    protected $casts = ['sampled_at' => 'datetime'];
}

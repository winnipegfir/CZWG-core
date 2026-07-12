<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
    protected $casts = [
        'enable_beta_components' => 'boolean',
        'enable_discord_notifications' => 'boolean',
    ];
}

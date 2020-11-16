<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class HomepageImages extends Model
{
    protected $fillable = [
        'url', 'credit'
    ];
}

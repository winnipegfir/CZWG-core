<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class SweatboxFile extends Model
{
    protected $fillable = [
        'position', 'name', 'description', 'file_url', 'updated_on', 'sort_order', 'updated_by',
    ];

    protected $casts = [
        'updated_on' => 'date',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'updated_by');
    }
}

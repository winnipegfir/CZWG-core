<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class VatcanMember extends Model
{
    protected $table = 'academy_vatcan_members';

    protected $fillable = [
        'cid', 'user_id', 'first_name', 'last_name', 'rating_id', 'rating_label',
        'entitled_course_slugs', 'active_home_member', 'first_seen_at', 'last_seen_at', 'last_synced_at',
    ];

    protected $casts = [
        'entitled_course_slugs' => 'array',
        'active_home_member' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];
}

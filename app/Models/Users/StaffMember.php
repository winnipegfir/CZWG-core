<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    protected $table = 'staff_member';

    protected $fillable = [
        'user_id', 'position', 'shortform', 'group', 'group_id', 'description', 'email',
        'out_until', 'contact_staff_member_id',
    ];

    protected $casts = [
        'out_until' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(StaffMember::class, 'contact_staff_member_id');
    }

    public function isOutOfOffice()
    {
        return $this->out_until !== null && $this->out_until->endOfDay()->isFuture();
    }
}

<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    protected $fillable = [
        'instructor_id', 'provider_user_id', 'student_id', 'start_time', 'end_time', 'status', 'note', 'network_callsign', 'instructor_comments', 'booked_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'booked_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function provider()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'provider_user_id');
    }

    public function confirmationContext(): string
    {
        return hash('sha256', implode('|', [
            $this->id, $this->provider_user_id, $this->student_id,
            $this->booked_at?->format('Y-m-d H:i:s.u'),
            $this->start_time?->format('Y-m-d H:i:s.u'),
            $this->end_time?->format('Y-m-d H:i:s.u'),
        ]));
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')->where('end_time', '>', now());
    }
}

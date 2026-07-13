<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    protected $fillable = [
        'instructor_id', 'student_id', 'start_time', 'end_time', 'status', 'note', 'network_callsign', 'instructor_comments', 'booked_at',
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

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')->where('end_time', '>', now());
    }
}

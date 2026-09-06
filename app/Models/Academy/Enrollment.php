<?php

namespace App\Models\Academy;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'academy_enrollments';
    protected $fillable = ['user_id', 'course_id', 'source', 'active', 'source_rating_id', 'source_synced_at', 'assigned_by', 'assigned_at', 'completed_at'];
    protected $casts = ['active' => 'boolean', 'source_synced_at' => 'datetime', 'assigned_at' => 'datetime', 'completed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function assigner() { return $this->belongsTo(User::class, 'assigned_by'); }
}

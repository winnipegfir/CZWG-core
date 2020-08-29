<?php

namespace App\Models\Publications;

use Illuminate\Database\Eloquent\Model;
use App\Models\Publications\Policy;
use Auth;

class PolicySection extends Model
{
    protected $table = 'policies_sections';

    protected $fillable = [
        'id', 'section_name',
    ];

    public function policies() {
        if(Auth::check() && Auth::user()->permissions >= 4) {
            return $this->hasMany(Policy::class, 'section_id');
        } else {
            return $this->hasMany(Policy::class, 'section_id')->where('staff_only', '0');
        }
    }
}

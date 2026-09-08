<?php
namespace App\Models\Academy;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
class ModuleProgress extends Model {
    protected $table = 'academy_module_progress';
    protected $fillable = ['user_id','module_id','viewed_at'];
    protected $casts = ['viewed_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function module() { return $this->belongsTo(Module::class); }
}

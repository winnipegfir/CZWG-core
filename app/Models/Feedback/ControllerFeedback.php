<?php

namespace App\Models\Feedback;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Parsedown;

class ControllerFeedback extends Model
{
    protected $hidden = ['id'];

    protected $fillable = [
        'user_id', 'controller_cid', 'position', 'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function html()
    {
        return new HtmlString(app(Parsedown::class)->text($this->content));
    }
}

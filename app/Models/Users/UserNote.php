<?php

namespace App\Models\Users;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Parsedown;

class UserNote extends Model
{
    protected $table = 'user_notes';

    protected $fillable = [
        'user_id', 'position', 'author', 'author_name', 'content', 'confidential', 'timestamp',
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

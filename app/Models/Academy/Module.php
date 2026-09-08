<?php

namespace App\Models\Academy;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'academy_modules';

    protected $fillable = ['course_id', 'title', 'slug', 'description', 'google_slides_url', 'slide_count', 'slide_asset_path', 'audio_url', 'sort_order', 'published'];

    protected $casts = ['published' => 'boolean', 'slide_count' => 'integer'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function googleSlidesEmbedUrl(): ?string
    {
        if (! $this->google_slides_url) {
            return null;
        }

        $url = html_entity_decode(trim($this->google_slides_url), ENT_QUOTES | ENT_HTML5);

        // Google "Publish to web" iframe URLs are already ready to embed.
        if (preg_match('~^https://docs\.google\.com/presentation/d/e/[a-zA-Z0-9_-]+/(?:pubembed|embed)(?:\?.*)?$~', $url)) {
            return $url;
        }

        // Keep backwards compatibility with ordinary Google Slides URLs saved by older Academy versions.
        if (preg_match('~^https://docs\.google\.com/presentation/d/([a-zA-Z0-9_-]+)(?:/[^?]*)?(?:\?.*)?$~', $url, $matches)) {
            return 'https://docs.google.com/presentation/d/'.$matches[1].'/embed?start=false&loop=false&delayms=3000';
        }

        return null;
    }

    public function googleSlidesEditorEmbedCode(): ?string
    {
        $url = $this->googleSlidesEmbedUrl();

        return $url ? '<iframe src="'.$url.'" frameborder="0" allowfullscreen="true"></iframe>' : null;
    }

    public function progress()
    {
        return $this->hasMany(ModuleProgress::class);
    }
}

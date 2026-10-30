<?php

namespace App\Services\SectorFiles;

use Illuminate\Support\Facades\Http;

class DiscordAnnouncement
{
    public function configured(): bool
    {
        $thread = (string) config('sector-files.discord_thread_id', '');
        if ($thread !== '' && !preg_match('/^[0-9]{17,20}$/D', $thread)) {
            return false;
        }
        return (bool) preg_match('~^https://discord\.com/api/webhooks/[0-9]+/[A-Za-z0-9_-]+$~', config('sector-files.discord_webhook') ?? '');
    }

    public function send(array $release): bool
    {
        if (!$this->configured()) {
            return false;
        }
        try {
            $url = config('sector-files.public_url');
            if (!filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
                return false;
            }
            $notes = $release['notes'] ?: 'No release notes provided.';
            if (mb_strlen($notes) > 3800) {
                $notes = mb_substr($notes, 0, 3800)."\n\nRead the complete release notes on the website.";
            }
            $query = ['wait' => 'true'];
            $thread = (string) config('sector-files.discord_thread_id', '');
            if ($thread !== '') {
                $query['thread_id'] = $thread;
            }
            return Http::connectTimeout(5)->timeout(10)->withOptions(['allow_redirects' => false])
                ->post(config('sector-files.discord_webhook').'?'.http_build_query($query), [
                    'content' => 'New Winnipeg FIR sector files are available: '.$url,
                    'embeds' => [[
                        'title' => mb_substr($release['name'], 0, 250),
                        'url' => $url,
                        'description' => $notes,
                        'color' => 1190724,
                        'fields' => [
                            ['name' => 'Version / AIRAC', 'value' => mb_substr($release['version'], 0, 64)],
                            ['name' => 'Downloads', 'value' => 'Full Install Package for new installations; Nav Data Update for existing installations. Both are available on the website.'],
                        ],
                    ]],
                    'allowed_mentions' => ['parse' => []],
                ])->successful();
        } catch (\Throwable $e) {
            // The webhook URL is a secret; do not report raw client exceptions.
            return false;
        }
    }
}

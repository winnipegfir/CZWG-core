<?php

namespace App\Services\SectorFiles;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GitHubReleases
{
    public const SCHEDULE = '<!-- winnipeg-sector-files:schedule:';

    public const ARCHIVED = '<!-- winnipeg-sector-files:archived -->';

    public function configured(): bool
    {
        return (bool) (config('sector-files.owner') && config('sector-files.repository')
            && config('sector-files.token') && config('sector-files.tag_prefix'));
    }

    private function path(): string
    {
        if (!$this->configured()) {
            throw new RuntimeException('Sector files are not configured.');
        }
        return '/repos/'.rawurlencode(config('sector-files.owner')).'/'.rawurlencode(config('sector-files.repository'));
    }

    private function client()
    {
        return Http::withToken(config('sector-files.token'))->acceptJson()
            ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28', 'User-Agent' => 'WinnipegFIR-SectorFiles'])
            ->connectTimeout(10)->timeout(30)->withOptions(['allow_redirects' => false]);
    }

    private function request(string $method, string $suffix, array $data = []): array
    {
        $response = $this->client()->send($method, 'https://api.github.com'.$this->path().$suffix,
            $method === 'GET' ? ['query' => $data] : ['json' => $data]);
        if (!$response->successful()) {
            // Never propagate response bodies or credentials to views/logs.
            throw new RuntimeException('GitHub could not complete the request (HTTP '.$response->status().').');
        }
        return $response->json() ?? [];
    }

    public function cacheKey(): string
    {
        return 'sector-files:v2:'.hash('sha256', config('sector-files.owner').'/'.config('sector-files.repository').'/'.config('sector-files.tag_prefix'));
    }

    public function forget(): void
    {
        Cache::forget($this->cacheKey());
    }

    public function all(bool $public = false): array
    {
        if ($public) {
            return Cache::remember($this->cacheKey(), 60, function () {
                if ($this->request('GET', '')['private'] ?? true) {
                    throw new RuntimeException('Public sector files require a public repository.');
                }
                return array_values(array_filter($this->all(), fn ($r) => !$r['draft'] && !$r['prerelease'] && ($r['packages_complete'] || $r['legacy_asset'])));
            });
        }
        $releases = [];
        // Fetch every page, rather than silently dropping older history after 30 releases.
        for ($page = 1; $page <= 100; $page++) {
            $batch = $this->request('GET', '/releases', ['per_page' => 100, 'page' => $page]);
            foreach ($batch as $release) {
                if ($this->managed($release)) {
                    $releases[] = $this->present($release);
                }
            }
            if (count($batch) < 100) {
                usort($releases, fn ($a, $b) => strcmp($b['published_at'] ?? $b['created_at'], $a['published_at'] ?? $a['created_at']));
                return $releases;
            }
        }
        throw new RuntimeException('Release history exceeds the supported limit.');
    }

    private function managed(array $release): bool
    {
        return str_starts_with($release['tag_name'] ?? '', config('sector-files.tag_prefix'));
    }

    public function get(int $id): array
    {
        $release = $this->request('GET', '/releases/'.$id);
        abort_unless($this->managed($release), 404);
        return $this->present($release);
    }

    private function present(array $release): array
    {
        $release['version'] = substr($release['tag_name'], strlen(config('sector-files.tag_prefix')));
        $release['archived'] = str_contains($release['body'] ?? '', self::ARCHIVED);
        $body = $release['body'] ?? '';
        $release['schedule'] = null;
        if (preg_match('/<!-- winnipeg-sector-files:schedule:([A-Za-z0-9+\/=]+) -->/', $body, $match)) {
            $schedule = json_decode(base64_decode($match[1], true) ?: '', true);
            if (is_array($schedule) && isset($schedule['at'], $schedule['announce'])
                && is_int($schedule['at']) && $schedule['at'] > 0 && is_bool($schedule['announce'])) {
                $release['schedule'] = $schedule;
            }
        }
        $release['notes'] = trim(preg_replace('/<!-- winnipeg-sector-files:schedule:.*? -->/s', '', str_replace(self::ARCHIVED, '', $body)));
        $release['full_asset'] = null;
        $release['nav_asset'] = null;
        $release['legacy_asset'] = null;
        foreach ($release['assets'] ?? [] as $asset) {
            if (($asset['state'] ?? '') !== 'uploaded' || !str_ends_with(strtolower($asset['name']), '.zip')) {
                continue;
            }
            if ($asset['name'] === 'winnipeg-full-install-'.$release['version'].'.zip') {
                $release['full_asset'] = $asset;
            } elseif ($asset['name'] === 'winnipeg-nav-data-'.$release['version'].'.zip') {
                $release['nav_asset'] = $asset;
            } else {
                $release['legacy_asset'] ??= $asset;
            }
        }
        $release['packages_complete'] = (bool) ($release['full_asset'] && $release['nav_asset']);
        // Never mislabel an older untyped ZIP as a full installation or nav-data update.
        if ($release['full_asset'] || $release['nav_asset']) {
            $release['legacy_asset'] = null;
        }
        return $release;
    }

    public function create(array $data, UploadedFile $fullZip, UploadedFile $navZip): array
    {
        $repository = $this->request('GET', '');
        if ($repository['private'] ?? true) {
            throw new RuntimeException('Use a public repository for public sector-file downloads.');
        }
        $release = $this->request('POST', '/releases', [
            'tag_name' => config('sector-files.tag_prefix').$data['version'],
            'name' => $data['title'], 'body' => $data['notes'] ?? '',
            'draft' => true, 'prerelease' => false,
        ]);
        $id = (int) $release['id'];
        $this->upload($id, $fullZip, 'winnipeg-full-install-'.$data['version'].'.zip');
        $this->upload($id, $navZip, 'winnipeg-nav-data-'.$data['version'].'.zip');
        if (!empty($data['publish_at'])) {
            return $this->schedule($id, $data['publish_at'], (bool) ($data['announce'] ?? false));
        }
        return $this->publish($id);
    }

    private function upload(int $id, UploadedFile $zip, string $name): void
    {
        // Fixed host: never forward credentials to an API-supplied upload URL.
        $stream = fopen($zip->getRealPath(), 'rb');
        if ($stream === false) {
            throw new RuntimeException('Could not read the upload.');
        }
        try {
            $response = $this->client()->timeout(180)->withBody($stream, 'application/zip')->post(
                'https://uploads.github.com'.$this->path().'/releases/'.$id.'/assets?name='.rawurlencode($name)
            );
            if (!$response->successful()) {
                throw new RuntimeException('ZIP upload failed. The unpublished draft is available for removal.');
            }
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    public function publish(int $id): array
    {
        $release = $this->get($id);
        if (!$release['draft'] || !$release['packages_complete']) {
            throw new RuntimeException('Both the full install and nav data ZIPs must finish uploading before publishing.');
        }
        $published = $this->request('PATCH', '/releases/'.$id, ['draft' => false, 'make_latest' => 'false', 'body' => $release['notes']]);
        $this->forget();
        return $this->present($published);
    }

    public function update(int $id, array $data): void
    {
        $release = $this->get($id);
        $this->request('PATCH', '/releases/'.$id, [
            'name' => $data['title'],
            'body' => ($data['notes'] ?? '').($release['archived'] ? "\n\n".self::ARCHIVED : '').$this->scheduleMarker($release['schedule']),
        ]);
        $this->forget();
    }

    private function scheduleMarker(?array $schedule): string
    {
        return $schedule ? "\n\n".self::SCHEDULE.base64_encode(json_encode($schedule)).' -->' : '';
    }

    public function schedule(int $id, ?string $at, bool $announce): array
    {
        $release = $this->get($id);
        if (!$release['draft'] || !$release['packages_complete']) {
            throw new RuntimeException('Only complete unpublished drafts can be scheduled.');
        }
        $schedule = null;
        if ($at) {
            if (!config('sector-files.scheduling_enabled')) {
                throw new RuntimeException('Scheduling is not enabled.');
            }
            $date = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $at, 'UTC');
            if (!$date || $date->format('Y-m-d\TH:i') !== $at || !$date->isFuture()) {
                throw new RuntimeException('Choose a future UTC date and time.');
            }
            $schedule = ['at' => $date->getTimestamp(), 'announce' => $announce];
        }
        $updated = $this->request('PATCH', '/releases/'.$id, [
            'body' => $release['notes'].$this->scheduleMarker($schedule),
        ]);
        $this->forget();
        return $this->present($updated);
    }

    public function archive(int $id, bool $archived): void
    {
        $release = $this->get($id);
        $this->request('PATCH', '/releases/'.$id, [
            'body' => $release['notes'].($archived ? "\n\n".self::ARCHIVED : ''),
        ]);
        $this->forget();
    }

    public function delete(int $id): void
    {
        $this->get($id); // Verify this is a sector release before any mutation.
        $this->request('DELETE', '/releases/'.$id);
        $this->forget(); // Git tags deliberately remain as history.
    }
}

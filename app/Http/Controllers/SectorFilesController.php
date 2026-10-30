<?php

namespace App\Http\Controllers;

use App\Services\SectorFiles\DiscordAnnouncement;
use App\Services\SectorFiles\GitHubReleases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class SectorFilesController extends Controller
{
    public function index(GitHubReleases $github)
    {
        $releases = [];
        $unavailable = false;
        try {
            $releases = $github->all(true);
        } catch (\Throwable $e) {
            $unavailable = true;
        }
        $latest = collect($releases)->first(fn ($r) => !$r['archived']);
        $older = array_values(array_filter($releases, fn ($r) => !$latest || $r['id'] !== $latest['id']));
        return response()->view('sector-files.index', compact('latest', 'older', 'unavailable'), $unavailable ? 503 : 200);
    }

    public function manage(GitHubReleases $github, DiscordAnnouncement $discord)
    {
        $releases = [];
        $unavailable = false;
        try {
            $releases = $github->all();
        } catch (\Throwable $e) {
            $unavailable = true;
        }
        return view('sector-files.manage', [
            'releases' => $releases, 'unavailable' => $unavailable,
            'configured' => $github->configured(), 'discordConfigured' => $discord->configured(),
        ]);
    }

    private function rules(): array
    {
        return ['title' => 'required|string|max:150', 'notes' => [
            'nullable', 'string', 'max:30000',
            function ($attribute, $value, $fail) {
                if (str_contains($value, GitHubReleases::ARCHIVED) || str_contains($value, GitHubReleases::SCHEDULE)) {
                    $fail('Release notes contain a reserved release marker.');
                }
            },
        ]];
    }

    private function mutation(GitHubReleases $github, callable $callback)
    {
        // Serialize staff changes. Shared cache storage is required for multiple app servers.
        $lock = Cache::lock($github->cacheKey().':write', 600);
        if (!$lock->get()) {
            return back()->withErrors(['release' => 'Another release change is in progress. Please try again shortly.']);
        }
        try {
            $message = $callback();
            return redirect()->route('sector-files.manage')->with('sector_status', $message);
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (\Throwable $e) {
            return redirect()->route('sector-files.manage')->withInput(request()->except('full_zip', 'nav_zip', '_token'))
                ->withErrors(['release' => 'GitHub could not confirm the change. Check the release list before retrying. If both ZIPs are uploaded, use Publish draft; otherwise delete the incomplete draft and upload both packages again. Check repository settings, token permissions, duplicate versions and GitHub availability.']);
        } finally {
            $lock->release();
        }
    }

    private function published(array $release, Request $request, DiscordAnnouncement $discord): string
    {
        if ($release['draft']) {
            return 'Both packages uploaded. Release scheduled in UTC; it remains hidden until publication.';
        }
        if ($request->boolean('announce') && !$discord->send($release)) {
            return 'Release published. The Discord announcement was not confirmed; check the channel before posting it manually.';
        }
        return 'Release published successfully.';
    }

    public function store(Request $request, GitHubReleases $github, DiscordAnnouncement $discord)
    {
        // An explicit immediate choice must never retain a stale scheduled time.
        if ($request->has('schedule_later') && !$request->boolean('schedule_later')) {
            $request->merge(['publish_at' => null]);
        }
        $data = $request->validate($this->rules() + [
            'version' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9]+(?:[._-][A-Za-z0-9]+)*$/'],
            'full_zip' => 'required|file|mimes:zip|max:'.config('sector-files.max_upload_kb'),
            'nav_zip' => 'required|file|mimes:zip|max:'.config('sector-files.max_upload_kb'),
            'announce' => 'sometimes|boolean',
            'schedule_later' => 'sometimes|boolean',
            'publish_at' => array_merge(['required_if:schedule_later,1'], $this->scheduleRules()),
        ]);
        return $this->mutation($github, fn () => $this->published($github->create($data, $request->file('full_zip'), $request->file('nav_zip')), $request, $discord));
    }

    private function scheduleRules(): array
    {
        return ['nullable', 'date_format:Y-m-d\TH:i', function ($attribute, $value, $fail) {
            if (!config('sector-files.scheduling_enabled')) {
                $fail('Scheduled publishing has not been enabled by the website administrator.');
                return;
            }
            try {
                $date = \Carbon\CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $value, 'UTC');
                if (!$date || $date->format('Y-m-d\TH:i') !== $value || !$date->isFuture()) {
                    $fail('Choose a future date and time in UTC.');
                }
            } catch (\Throwable $e) {
                $fail('Choose a valid UTC date and time.');
            }
        }];
    }

    public function schedule(Request $request, int $release, GitHubReleases $github)
    {
        $data = $request->validate(['publish_at' => $this->scheduleRules(), 'announce' => 'sometimes|boolean']);
        return $this->mutation($github, function () use ($github, $release, $data, $request) {
            $github->schedule($release, $data['publish_at'] ?? null, $request->boolean('announce'));
            return empty($data['publish_at']) ? 'Schedule cancelled. Release remains an unpublished draft.' : 'Publication schedule updated (UTC).';
        });
    }

    public function publish(Request $request, int $release, GitHubReleases $github, DiscordAnnouncement $discord)
    {
        $request->validate(['announce' => 'sometimes|boolean']);
        return $this->mutation($github, fn () => $this->published($github->publish($release), $request, $discord));
    }

    public function update(Request $request, int $release, GitHubReleases $github)
    {
        $data = $request->validate($this->rules());
        return $this->mutation($github, function () use ($github, $release, $data) {
            $github->update($release, $data);
            return 'Release details updated.';
        });
    }

    public function archive(Request $request, int $release, GitHubReleases $github)
    {
        $data = $request->validate(['archived' => 'required|boolean']);
        return $this->mutation($github, function () use ($github, $release, $data) {
            $github->archive($release, (bool) $data['archived']);
            return $data['archived'] ? 'Release archived. Its download remains available.' : 'Release restored.';
        });
    }

    public function destroy(Request $request, int $release, GitHubReleases $github)
    {
        $request->validate(['confirm_delete' => 'accepted']);
        return $this->mutation($github, function () use ($github, $release) {
            $github->delete($release);
            return 'Release and ZIP packages deleted from GitHub. The Git tag was retained.';
        });
    }
}

<?php

namespace App\Console\Commands;

use App\Services\SectorFiles\DiscordAnnouncement;
use App\Services\SectorFiles\GitHubReleases;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PublishDueSectorFiles extends Command
{
    protected $signature = 'sector-files:publish-due';
    protected $description = 'Publish complete sector-file drafts whose UTC schedule is due';

    public function handle(GitHubReleases $github, DiscordAnnouncement $discord): int
    {
        if (!config('sector-files.scheduling_enabled') || !$github->configured()) {
            return 0;
        }
        // Same lock as staff actions: prevent concurrent publish/delete/reschedule.
        $lock = Cache::lock($github->cacheKey().':write', 600);
        if (!$lock->get()) {
            return 0;
        }
        $failed = false;
        try {
            foreach ($github->all() as $release) {
                if (!$release['draft'] || !$release['packages_complete'] || !$release['schedule']
                    || $release['schedule']['at'] > now('UTC')->timestamp) {
                    continue;
                }
                try {
                    $published = $github->publish((int) $release['id']);
                    // No retry after publication: an uncertain Discord response must
                    // not cause repeated announcements on every scheduler run.
                    if ($release['schedule']['announce'] && !$discord->send($published)) {
                        $failed = true;
                        Log::warning('Sector release published; Discord delivery unconfirmed. Check channel before manual announcement.', ['release_id' => $release['id']]);
                    }
                    $this->info('Published sector release '.$release['id']);
                } catch (\Throwable $e) {
                    $failed = true;
                    Log::warning('Scheduled sector publication unconfirmed. Inspect GitHub release status.', ['release_id' => $release['id']]);
                }
            }
        } catch (\Throwable $e) {
            $failed = true;
            Log::warning('Could not load scheduled sector releases. Check GitHub configuration and availability.');
        } finally {
            $lock->release();
        }
        return $failed ? 1 : 0;
    }
}

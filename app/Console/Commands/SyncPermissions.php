<?php

namespace App\Console\Commands;

use App\Models\Users\User;
use App\Services\PermissionSyncService;
use Illuminate\Console\Command;

// One-time (or as-needed) backfill: PermissionSyncService only reacts to roster/
// instructor/staff records being saved going forward, so it doesn't retroactively
// touch anyone already on the books. This walks every user and applies the same
// derivation, so the site's existing permissions catch up to current roster data.
class SyncPermissions extends Command
{
    protected $signature = 'winnipeg:sync-permissions {--dry-run : Preview changes without saving them}';

    protected $description = 'Backfill users.permissions from current roster/instructor/staff data.';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $changed = 0;

        User::chunkById(200, function ($users) use (&$changed, $dryRun) {
            foreach ($users as $user) {
                $target = PermissionSyncService::pendingChangeFor($user);
                if ($target === null) {
                    continue;
                }

                $changed++;
                $this->line(sprintf(
                    '%s CID %s: %d -> %d',
                    $dryRun ? '[dry-run]' : '[updated]',
                    $user->id,
                    $user->permissions,
                    $target
                ));

                if (! $dryRun) {
                    $user->permissions = $target;
                    $user->save();
                }
            }
        });

        $this->info($changed === 0
            ? 'Nothing to change -- everyone already matches their roster data.'
            : sprintf('%s %d user(s).', $dryRun ? 'Would change' : 'Changed', $changed));

        return 0;
    }
}

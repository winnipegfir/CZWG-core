<?php

namespace App\Console\Commands;

use App\Services\AcademyVatcanSyncService;
use Illuminate\Console\Command;

class SyncAcademyVatcanRoster extends Command
{
    protected $signature = 'academy:sync-vatcan-roster';

    protected $description = 'Synchronize Academy access from the VATCAN Winnipeg home and visiting-controller rosters';

    public function handle(AcademyVatcanSyncService $sync): int
    {
        $run = $sync->sync();

        if ($run->status !== 'success') {
            $this->error($run->message ?: 'VATCAN Academy sync failed.');
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Synced %d home controllers and %d visitors: %d matched accounts and %d pending CIDs.',
            $run->controllers_found,
            $run->visitors_ignored,
            $run->users_matched,
            $run->pending_cids
        ));

        return self::SUCCESS;
    }
}

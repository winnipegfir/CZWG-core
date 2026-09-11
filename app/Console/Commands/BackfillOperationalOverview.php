<?php

namespace App\Console\Commands;

use App\Services\StatSimOperationalBackfill;
use Illuminate\Console\Command;

class BackfillOperationalOverview extends Command
{
    protected $signature = 'winnipeg:backfill-operational-overview
        {--days=1 : Number of UTC days to import (1-90)}
        {--airport=CYWG : CYWG, CYXE, CYQR, CYQT, or ALL}
        {--dry-run : Read and validate StatSim data without writing to the database}';

    protected $description = 'Backfill Operational Overview flight history from StatSim';

    public function handle(StatSimOperationalBackfill $backfill): int
    {
        $days = (int) $this->option('days');
        $airport = strtoupper(trim((string) $this->option('airport')));
        $allowed = array_merge(array_keys(StatSimOperationalBackfill::AIRPORTS), ['ALL']);

        if ($days < 1 || $days > 90) {
            $this->error('Days must be between 1 and 90.');
            return self::INVALID;
        }
        if (! in_array($airport, $allowed, true)) {
            $this->error('Airport must be CYWG, CYXE, CYQR, CYQT, or ALL.');
            return self::INVALID;
        }
        if (! getenv('STATSIM_API_KEY')) {
            $this->error('STATSIM_API_KEY is unavailable to this command.');
            return self::FAILURE;
        }

        $airports = $airport === 'ALL' ? array_keys(StatSimOperationalBackfill::AIRPORTS) : [$airport];
        $dryRun = (bool) $this->option('dry-run');
        $this->info(($dryRun ? 'Dry run: ' : '').'importing '.$days.' day(s) for '.implode(', ', $airports).'.');

        try {
            $result = $backfill->run($days, $airports, $dryRun, function (string $message) {
                $this->line($message);
            });
        } catch (\Throwable $exception) {
            report($exception);
            $this->error('Import stopped safely: '.$exception->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->table(['Flights returned', 'Flights processed', 'Flights skipped', 'Position points', 'Database writes'], [[
            $result['returned'], $result['processed'], $result['skipped'], $result['positions'],
            $dryRun ? '0 (dry run)' : $result['written'],
        ]]);
        $this->info($dryRun ? 'Dry run completed; the database was not changed.' : 'Historical import completed.');
        return self::SUCCESS;
    }
}

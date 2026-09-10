<?php

namespace App\Console\Commands;

use App\Services\OperationalOverviewCollector;
use Illuminate\Console\Command;
use Throwable;

class CollectOperationalOverview extends Command
{
    protected $signature = 'winnipeg:operational-overview';
    protected $description = 'Collect a summarized Winnipeg FIR airport operational sample from the VATSIM live feed';

    public function handle(OperationalOverviewCollector $collector): int
    {
        try {
            $result = $collector->collect();
            if ($result['skipped']) {
                $this->warn('Operational sample skipped: database tables are not installed yet. Run php artisan migrate.');
                return self::SUCCESS;
            }
            $this->info(sprintf(
                'Live VATSIM sample collected at %s: %d Winnipeg controller(s), %d tracked aircraft, %d tracked flight(s).',
                $result['sampled_at']->toDateTimeString(), $result['controllers'], $result['tracked_aircraft'], $result['tracked_flights']
            ));
            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Operational sample failed: '.$exception->getMessage());
            return self::FAILURE;
        }
    }
}

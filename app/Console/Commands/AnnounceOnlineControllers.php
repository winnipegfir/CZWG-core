<?php

namespace App\Console\Commands;

use App\Services\ControllerOnlineAlerts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnnounceOnlineControllers extends Command
{
    protected $signature = 'controllers:announce-online';
    protected $description = 'Announce Winnipeg controllers and privately flag roster/rating checks';

    public function handle(ControllerOnlineAlerts $alerts): int
    {
        try {
            $this->info('Sent '.$alerts->run().' controller alerts.');
            return 0;
        } catch (\Throwable $e) {
            Log::warning('Controller alert poll failed. Check feed availability, database migrations and configuration.');
            $this->error('Controller alerts could not be checked. No authorization conclusion should be drawn from a failed poll.');
            return 1;
        }
    }
}

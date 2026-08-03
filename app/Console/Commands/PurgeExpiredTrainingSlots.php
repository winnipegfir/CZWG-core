<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\TrainingSession;
use Illuminate\Console\Command;

class PurgeExpiredTrainingSlots extends Command
{
    protected $signature   = 'training:purge-expired-slots';
    protected $description = 'Delete open (unbooked) training session slots whose end time has passed.';

    public function handle(): void
    {
        TrainingSession::where('status', 'open')
            ->where('end_time', '<', now())
            ->delete();
    }
}

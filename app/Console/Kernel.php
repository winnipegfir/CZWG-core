<?php

namespace App\Console;

use App\Console\Commands\ActivityLog;
use App\Console\Commands\CheckVisitHours;
use App\Console\Commands\CurrencyCheck;
use App\Console\Commands\EventReminders;
use App\Console\Commands\RatingUpdate;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // * * * * * schedulers
        $schedule->command(ActivityLog::class)->everyMinute()->evenInMaintenanceMode();
        $schedule->command(EventReminders::class)->everyMinute();
        $schedule->call(function () { file_get_contents(config('cronurls.minute')); })->everyMinute();

        // 0 0 * * * schedulers
        $schedule->command(RatingUpdate::class)->daily();
        $schedule->call(function () { file_get_contents(config('cronurls.daily')); })->daily();

        // 0 0 1 * * schedulers
        $schedule->command(CheckVisitHours::class)->monthly();
        $schedule->command(CurrencyCheck::class)->monthly();
        $schedule->call(function () { file_get_contents(config('cronurls.monthly')); })->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

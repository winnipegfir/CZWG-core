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
        $schedule->call(function () use ($schedule) {
            $schedule->command(ActivityLog::class)->evenInMaintenanceMode();
            $schedule->command(EventReminders::class);

            file_get_contents(config('cronurls.minute'));
        })->everyMinute();

        $schedule->call(function () use ($schedule) {
            $schedule->command(RatingUpdate::class);

            file_get_contents(config('cronurls.daily'));
        })->weekly();

        $schedule->call(function () use ($schedule) {
            $schedule->command(CheckVisitHours::class);
            $schedule->command(CurrencyCheck::class);

            file_get_contents(config('cronurls.monthly'));
        })->monthly();
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

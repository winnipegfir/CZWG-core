<?php

namespace App\Console\Commands;

use App\Models\Events\Event;
use App\Models\Events\EventConfirm;
use App\Notifications\events\EventReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and send event reminders to controllers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = Event::where('start_timestamp', '<', Carbon::now()->addDay())
            ->where('start_timestamp', '>', Carbon::now())
            ->get();

        foreach ($events as $e) {
            $confirms = EventConfirm::where('event_id', $e->id)->get();

            foreach ($confirms as $c) {
                if ($c->email_sent != 0 && $c->user()->gdpr_subscribed_emails != 1) {
                    return;
                }

                $positions = EventConfirm::where(['user_id' => $c->user_id, 'event_id' => $e->id])->update('email_sent', 1)->get();

                $c->user()->notify(new EventReminder($e, $positions));
            }
        }
    }
}

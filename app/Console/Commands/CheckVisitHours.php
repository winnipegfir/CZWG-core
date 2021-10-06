<?php

namespace App\Console\Commands;

use App\Models\AtcTraining\RosterMember;
use App\Models\Settings\CoreSettings;
use App\Notifications\network\CheckVisitHours as Email;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckVisitHours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:visit-hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if controllers have put 50% of their time on Winnipeg positions';

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
        $members = [];

        function getUrl($cid, $pageNum, $date)
        {
            return sprintf('https://api.vatsim.net/api/ratings/%s/atcsessions/?page=%s&start=%s', $cid, $pageNum, $date);
        }

        foreach (RosterMember::all() as $r) {
            // Set our variables
            $monthAgo = Carbon::now()->subMonth()->format('Y-m-d');
            $pageNum = 1;
            $minutes = 0;

            for ($x = 1; $x <= 1; $x++) {
                $client = new Client();
                $response = $client->request('GET', getUrl($r->cid, $pageNum, $monthAgo));
                $contents = json_decode($response->getBody()->getContents());

                if ($contents->next) {
                    $pageNum++;
                    $x = 0;
                }

                foreach ($contents->results as $result) {
                    $minutes += $result->minutes_on_callsign;
                }
            }

            // Change to hours as that is how it's stored in the roster
            $hours = $minutes / 60;

            $quotient = $hours == 0 ? 0 : round($r->currency / $hours, 3);

            // Winnipeg Hours / VATSIM Total is less than 50%
            if ($quotient < 0.5) {
                $name = $r->full_name.' '.$r->cid;

                $members[] = [
                    'percentage' => $quotient,
                    'name' => $name,
                ];
            }
        }

        krsort($members);

        $settings = CoreSettings::find(1);
        Notification::route('mail', [
            $settings->emailfirchief,
            $settings->emaildepfirchief,
            $settings->emailcinstructor,
        ])->notify(new Email($members));
    }
}

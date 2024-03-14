<?php

namespace App\Console\Commands;

use App\Classes\HttpHelper;
use App\Classes\VatsimHelper;
use App\Models\Users\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RatingUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'winnipeg:rating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs VATSIM rating update';

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
        $ratings = [];

        $response = HttpHelper::getClient()->get(VatsimHelper::getDatafeedUrl());
        $vatsimRatings = $response->object();

        foreach ($vatsimRatings as $r) {
            $ratings[$r->id] = [
                'short' => $r->short,
                'long' => $r->long,
            ];
        }

        $users = User::where('id', '!=', 1)->where('id', '!=', 2)->get();

        // Check each user to see if their rating has changed
        foreach ($users as $u) {
            $getRating = json_decode(file_get_contents('https://api.vatsim.net/api/ratings/'.$u->id.'/'));
            $ratingID = $getRating->rating;

            if ($u->rating_id != $ratingID) {
                //Log it for when it breaks
                Log::info('User: '.$u->fname.' '.$u->lname.' updated from '.$u->rating_short.' to '.$ratings[$ratingID]['short'].'.');

                $u->rating_id = $ratingID;
                $u->rating_short = $ratings[$ratingID]['short'];
                $u->rating_long = $ratings[$ratingID]['long'];
                $u->rating_grp = $ratings[$ratingID]['long'];
                $u->save();

                $rosterMember = $u->rosterProfile()->first();

                if ($rosterMember) {
                    Log::info('--> Setting rating hours to 0');

                    $rosterMember->rating_hours = 0;
                    $rosterMember->save();
                }

                Log::info('--> Completed!');
            } else {
                //Log it for when it breaks
                Log::info('User: '.$u->fname.' '.$u->lname.' rating is unchanged. Skipping.');
            }
        }
    }
}

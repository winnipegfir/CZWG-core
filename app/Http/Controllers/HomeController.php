<?php

namespace App\Http\Controllers;

use App\Models\AtcTraining\RosterMember;
use App\Models\Events\Event;
use App\Models\News\News;
use App\Models\Settings\HomepageImages;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class HomeController extends Controller
{
    public function view()
    {
        //Winnipeg online controllers
        $client = new Client();
        $response = $client->request('GET', 'https://data.vatsim.net/v3/vatsim-data.json');
        $controllers = json_decode($response->getBody()->getContents())->controllers;

        $finalPositions = array();

        $prefixes = [
            'WPG_',
            'CZWG_',
            'ZWG_',
            'CYWG_',
            'CYPG_',
            'CYAV_',
            'CYXE_',
            'CYQR_',
            'CYQT_',
            'CYMJ_',
        ];

        foreach ($controllers as $c) {
            if (Str::startsWith($c->callsign, $prefixes) && !Str::endsWith($c->callsign, ['ATIS', 'OBS']) && $c->facility != 0) {
                $finalPositions[] = $c;
            }
        }

        //News
        $news = News::where('visible', true)->get()->sortByDesc('published')->take(3);

        //Event
        $nextEvents = Event::where('start_timestamp', '>', Carbon::now())->get()->sortBy('start_timestamp')->take(3);

        //Top Controllers
        $topControllersArray = [];

        $colourArray = [
            0 => 'gold',
            1 => 'grey',
            2 => '#8e3c00',
            3 => 'lightgray',
            4 => 'lightgray',
        ];

        $topControllers = RosterMember::where('currency', '!=', 0)->get()->sortByDesc('currency');

        $n = -1;
        foreach($topControllers as $top) {
            $top = [
                'id' => $n += 1,
                'cid' => $top['user_id'],
                'time' => decimal_to_hm($top['currency']),
                'colour' => $colourArray[$n]
            ];
            array_push($topControllersArray, $top);
        }

        //Weather
        $weather = Cache::remember('weather.data', 900, function () {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.checkwx.com/metar/CYWG,CYXE,CYQR,CYQT,CYPG,CYMJ/decoded?pretty=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: ' . env('AIRPORT_API_KEY')]);

            $resp = json_decode(curl_exec($ch));

            curl_close($ch);

            foreach ($resp->data as $w) {
                switch ($w->icao) {
                    case "CYWG":
                        $weatherArray[0] = $w;
                        break;
                    case "CYXE":
                        $weatherArray[1] = $w;
                        break;
                    case "CYQT":
                        $weatherArray[2] = $w;
                        break;
                    case "CYQR":
                        $weatherArray[3] = $w;
                        break;
                    case "CYMJ":
                        $weatherArray[4] = $w;
                        break;
                    case "CYPG":
                        $weatherArray[5] = $w;
                        break;
                }
            }

            ksort($weatherArray);
            return($weatherArray);
        });

        //Background Image
        $background = HomepageImages::all()->random();

        return view('index', compact('finalPositions','news', 'nextEvents', 'topControllersArray', 'weather', 'background'));
    }

    public function nate() {
        function getStuff($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $json = json_decode(curl_exec($ch));
            curl_close($ch);

            return $json;
        }

            $hours = getStuff('https://api.vatsim.net/api/ratings/1233493/rating_times/');

            $atcTime = decimal_to_hm($hours->atc);
            $pilotTime = decimal_to_hm($hours->pilot);
            $totalTime = decimal_to_hm($hours->atc + $hours->pilot);

            $timeOnNetwork = str_replace("T", " ", getStuff('https://api.vatsim.net/api/ratings/1233493/')->reg_date);
            $yearsOnNetwork = Carbon::now()->diffInYears($timeOnNetwork);

        return view('nate', compact('atcTime', 'pilotTime', 'totalTime', 'yearsOnNetwork'));
    }
}

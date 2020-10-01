<?php

namespace App\Http\Controllers;

use App\Models\AtcTraining\RosterMember;
use App\Models\Events\Event;
use App\Models\News\News;
use App\Models\News\CarouselItem;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class HomeController extends Controller
{
    public function view()
    {
        //VATSIM online controllers
        $vatsim = new \Vatsimphp\VatsimData();
        $allPositions=[];
        $vatsim->setConfig('cacheOnly', false);
        $planes = null;
        $finalPositions = array();
        if ($vatsim->loadData())
            $centreControllers = $vatsim->searchCallsign('ZWG_');
            $winnipegControllers = $vatsim->searchCallsign('CYWG_');
            $portageControllers = $vatsim->searchCallsign('CYPG_');
            $standrewsControllers = $vatsim->searchCallsign('CYAV_');
            $saskatoonControllers = $vatsim->searchCallsign('CYXE_');
            $reginaControllers = $vatsim->searchCallsign('CYQR_');
            $thunderbayControllers = $vatsim->searchCallsign('CYQT_');
            $moosejawControllers = $vatsim->searchCallsign('CYMJ_');
            array_push($allPositions, $centreControllers->toArray(), $winnipegControllers->toArray(), $portageControllers->toArray(), $standrewsControllers->toArray(), $saskatoonControllers->toArray(), $reginaControllers->toArray(), $thunderbayControllers->toArray(), $moosejawControllers->toArray());

            foreach($allPositions as $controller) {
                foreach ($controller as $c)
                    if (Str::endsWith($c['callsign'], '_ATIS') || Str::endsWith($c['callsign'], '_OBS') || $c['facilitytype'] == 0) {
                        continue;
                    } else {
                        $finalPositions[] = $c;
                    }
            }

            //$topHomeControllers

            $planes = $vatsim->getPilots()->toArray();

        //News
        $news = News::where('visible', true)->get()->sortByDesc('published')->take(3);

        //Event
        $nextEvent = Event::where('start_timestamp', '>', Carbon::now())->get()->sortByDesc('id')->first();

        $topControllersArray = [];

        $topControllers = RosterMember::all()->sortByDesc('currency');
        foreach($topControllers as $top) {
            $top = [
                'cid' => $top['user_id'],
                'time' => decimal_to_hm($top['currency'])];
            array_push($topControllersArray, $top);
        }

        return view('index', compact('finalPositions', 'news', 'planes', 'nextEvent', 'topControllersArray'));
    }

    public function map()
    {
        //VATSIM online controllers
        $vatsim = new \Vatsimphp\VatsimData();
        $vatsim->setConfig('cacheOnly', false);
        $allPositions=[];
        $planes = null;
        if ($vatsim->loadData()) {
            $centreControllers = $vatsim->searchCallsign('CZWG_');
            $winnipegControllers = $vatsim->searchCallsign('CYWG_');
            $portageControllers = $vatsim->searchCallsign('CYPG_');
            $standrewsControllers = $vatsim->searchCallsign('CYAV_');
            $saskatoonControllers = $vatsim->searchCallsign('CYXE_');
            $reginaControllers = $vatsim->searchCallsign('CYQR_');
            $thunderbayControllers = $vatsim->searchCallsign('CYQT_');
            $moosejawControllers = $vatsim->searchCallsign('CYMJ_');
            array_push($allPositions, $centreControllers->toArray(), $winnipegControllers->toArray(), $portageControllers->toArray(), $standrewsControllers->toArray(), $saskatoonControllers->toArray(), $reginaControllers->toArray(), $thunderbayControllers->toArray(), $moosejawControllers->toArray());

            foreach($allPositions as $controller){
                foreach($controller as $c)
                    if(!Str::endsWith($c['callsign'], '_ATIS'))
                        $finalPositions[] = $c;
            }
            $planes = $vatsim->getPilots()->toArray();
        }
        return view('map', compact('finalPositions', 'planes'));
    }

    public function airports() {
        function checkAtis($icao) {
            $vatsim = new \Vatsimphp\VatsimData();
            $vatsim->setConfig('cacheOnly', false);
            $callsign = $icao.'_ATIS';
            if ($vatsim->loadData()) {
                if ($vatsim->searchCallsign($callsign)->toArray() == true) {
                    $callsignArray = $vatsim->searchCallsign($callsign)->toArray();
                    if (!$callsignArray == null) {

                        $atis = $callsignArray[0]['atis_message'];
                        $atis = str_replace('^Â§', " ", $atis);
                    } else {
                        $atis = $vatsim->getMetar($icao);
                        if($atis == "") {
                            $atis = "No ATIS or METAR could be found.";
                        }
                    }
                } else {
                    $atis = $vatsim->getMetar($icao);
                    if($atis == "") {
                        $atis = "No ATIS or METAR could be found.";
                    }
                }
            } else {
                if (curl_setopt(curl_init(), CURLOPT_URL, 'http://metar.vatsim.net/metar.php?id='.$icao) == true) {
                    $url = 'http://metar.vatsim.net/metar.php?id='.$icao;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $metar = curl_exec($ch);
                    curl_close($ch);

                    $atis = $metar;

                    if($atis == "") {
                        $atis = "No ATIS or METAR could be found.";
                    }

                } else {
                    $atis = "No ATIS or METAR could be found.";
                }
            }
            return $atis;
        }

        function getAtisLetter($icao) {
            $atis_letter = null;
            $vatsim = new \Vatsimphp\VatsimData();
            $vatsim->setConfig('cacheOnly', false);
            $callsign = $icao.'_ATIS';
            if ($vatsim->loadData()) {
                if ($vatsim->searchCallsign($callsign)->toArray() == true) {
                    $callsignArray = $vatsim->searchCallsign($callsign)->toArray();
                    if (!$callsignArray == null) {
                        $atis = $callsignArray[0]['atis_message'];
                        $atis_letter = substr($atis, -2, 1);
                    } else {
                        $atis_letter = "?";
                    }
                }
            }
            return $atis_letter;
        }

        $cywg = checkAtis('CYWG');
        $cywgLetter = getAtisLetter('CYWG');
        $cypg = checkAtis('CYPG');
        $cypgLetter = getAtisLetter('CYPG');
        $cyxe = checkAtis('CYXE');
        $cyxeLetter = getAtisLetter('CYXE');
        $cyqt = checkAtis('CYQT');
        $cyqtLetter = getAtisLetter('CYQT');
        $cyqr = checkAtis('CYQR');
        $cyqrLetter = getAtisLetter('CYQR');
        $cymj = checkAtis('CYMJ');
        $cymjLetter = getAtisLetter('CYMJ');

        return view('airports', compact('cywg', 'cywgLetter', 'cypg', 'cypgLetter', 'cyxe', 'cyxeLetter', 'cyqt', 'cyqtLetter', 'cyqr', 'cyqrLetter', 'cymj', 'cymjLetter'));
    }

    public function nate() {
            $url = 'https://api.vatsim.net/api/ratings/1233493/rating_times/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $json = curl_exec($ch);
            curl_close($ch);

            $atcTime = json_decode($json)->atc;
            $pilotTime = json_decode($json)->pilot;
            $totalTime = $atcTime + $pilotTime;

        return view('nate', compact('atcTime', 'pilotTime', 'totalTime'));
    }
}

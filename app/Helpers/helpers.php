<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

function decimal_to_hm($decimal) {
    $seconds = ($decimal * 3600);
    $hours = floor($decimal);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);

    return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT);
}

/* function checkAtis($icao) {
    $vatsim = new \Vatsimphp\VatsimData();
    $vatsim->setConfig('cacheOnly', false);
    $vatsim->setConfig('dataRefresh', 60);
    $callsign = $icao . '_ATIS';
    if ($vatsim->loadData()) {
        if ($vatsim->searchCallsign($callsign)->toArray() == true) {
            $callsignArray = $vatsim->searchCallsign($callsign)->toArray();
            if (!$callsignArray == null) {

                $atis = $callsignArray[0]['atis_message'];
                $atis = str_replace('^Â§', " ", $atis);
            } else {
                $atis = $vatsim->getMetar($icao);
                if ($atis == "") {
                    $atis = "No ATIS or METAR could be found.";
                }
            }
        } else {
            $atis = $vatsim->getMetar($icao);
            if ($atis == "") {
                $atis = "No ATIS or METAR could be found.";
            }
        }
    } else {
        if (curl_setopt(curl_init(), CURLOPT_URL, 'https://api.checkwx.com/metar/' . $icao . '/decoded') == true) {
            $url = 'https://api.checkwx.com/metar/' . $icao . '/decoded';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-Key: ' . env('AIRPORT_API_KEY')]);
            $metar = curl_exec($ch);
            curl_close($ch);

            $atis = $metar;

            if ($atis == "") {
                $atis = "No ATIS or METAR could be found.";
            }

        } else {
            $atis = "No ATIS or METAR could be found.";
        }
    }
    return $atis;
} */

function checkAtis($icao) {
    $atis_letter = null;
    $text_atis = '';

    $client = new Client();
    $response = $client->request('GET', 'https://data.vatsim.net/v3/vatsim-data.json');
    $atis = json_decode($response->getBody()->getContents())->atis;

    foreach ($atis as $a) {
        if (Str::startsWith($a->callsign, $icao)) {
            $text_atis = $a->text_atis;
        }
    }

    if ($text_atis) {
        $text = '';
        foreach ($text_atis as $t) {
            $text .= $t . " ";
        }
    } else {
        $text = Cache::remember('metar.data.' . $icao, 900, function () use ($icao) {
            $c = new Client();
            $res = $c->request('GET', 'https://api.checkwx.com/metar/' . $icao, [
                'headers' => [
                    'X-API-Key' => env('AIRPORT_API_KEY')
                ]
            ]);

            return json_decode($res->getBody()->getContents())->data[0];
        });
    }

    return $text;
}

function getAtisLetter($icao) {
    $atis_letter = null;

    $client = new Client();
    $response = $client->request('GET', 'https://data.vatsim.net/v3/vatsim-data.json');
    $atis = json_decode($response->getBody()->getContents())->atis;

    foreach ($atis as $a) {
        if (Str::startsWith($a->callsign, $icao)) {
            $atis_letter = $a->atis_code;
        }
    }

    return $atis_letter;
}

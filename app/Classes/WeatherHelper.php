<?php

namespace App\Classes;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WeatherHelper {
    /**
     * Gets ATIS Letter for Winnipeg Airports Page
     * @param $icao
     * @return string|null
     */
    public static function getAtisLetter($icao) {
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

    /**
     * Gets ATIS Letter for Winnipeg Airports Page
     * @param $icao
     * @return string
     */
    public static function getAtis($icao) {
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
                $text .= $t.' ';
            }
        } else {
            $text = Cache::remember('metar.data.'.$icao, 900, function () use ($icao) {
                $c = new Client();
                $res = $c->request('GET', 'https://api.checkwx.com/metar/'.$icao, [
                    'headers' => [
                        'X-API-Key' => env('AIRPORT_API_KEY'),
                    ],
                ]);

                $metar = json_decode($res->getBody()->getContents())->data;

                if (! $metar) {
                    return 'No ATIS/METAR available.';
                }

                return $metar[0];
            });
        }

        return $text;
    }
}

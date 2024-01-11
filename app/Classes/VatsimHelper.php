<?php

namespace App\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

class VatsimHelper
{
    public static function getDatafeedUrl(): string
    {
        $url = null;

        try {
            $url = Cache::remember('vatsim-datafeed-url', 86400, function () {
                $client = new Client();
                $request = $client->request('GET', 'https://status.vatsim.net/status.json');
                return json_decode($request->getBody()->getContents())->data->v3[0];
            });
        } catch (GuzzleException $e) {

        }

        return $url;
    }
}

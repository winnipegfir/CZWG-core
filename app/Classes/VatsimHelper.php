<?php

namespace App\Classes;

use GuzzleHttp\Client;

class VatsimHelper
{
    public static function getDatafeedUrl()
    {
        $client = new Client();
        $request = $client->request('GET', 'https://status.vatsim.net/status.json');

        return json_decode($request->getBody()->getContents())->data->v3[0];
    }
}

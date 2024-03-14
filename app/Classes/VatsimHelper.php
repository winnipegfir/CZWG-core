<?php

namespace App\Classes;

use Illuminate\Support\Facades\Cache;

class VatsimHelper
{
    public static function getDatafeedUrl(): string
    {
        return Cache::remember('vatsim-datafeed-url', 86400, function () {
            $request = HttpHelper::getClient()->get('https://status.vatsim.net/status.json');
            return $request['data']['v3'][0];
        });
    }
}

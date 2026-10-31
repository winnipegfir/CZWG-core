<?php

return [
    'enabled' => env('CONTROLLER_ALERTS_ENABLED', false),
    'online_webhook' => env('CONTROLLER_ALERTS_ONLINE_WEBHOOK'),
    'review_webhook' => env('CONTROLLER_ALERTS_REVIEW_WEBHOOK'),
    // Exact first callsign segment, including the website's established aliases.
    'prefixes' => \App\Services\ControllerActivityService::HOME_FIR_PREFIXES,
    'max_feed_age_seconds' => 300,
];

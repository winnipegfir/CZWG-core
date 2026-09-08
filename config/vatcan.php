<?php

return [
    'api_key' => env('VATCAN_API_KEY'),
    'api_url' => env('VATCAN_API_URL', 'https://vatcan.ca/api/v2'),
    'public_roster_url' => env('VATCAN_PUBLIC_ROSTER_URL', 'https://vatcan.ca/division/facility/CZWG'),
    'public_roster_min_members' => (int) env('VATCAN_PUBLIC_ROSTER_MIN_MEMBERS', 5),
    'public_roster_max_drop_percent' => (int) env('VATCAN_PUBLIC_ROSTER_MAX_DROP_PERCENT', 25),
    'public_roster_user_agent' => env('VATCAN_PUBLIC_ROSTER_USER_AGENT', 'Winnipeg FIR Academy roster sync'),
];

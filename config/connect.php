<?php

return [
    // env('test') => .ENV -> TEST
    // config('client_id') 
    'client_id' => env('CONNECT_CLIENT_ID'),
    'secret' => env('CONNECT_SECRET'),
    'redirect' => env('CONNECT_REDIRECT_URI')
];

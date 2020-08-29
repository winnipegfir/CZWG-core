<?php

return [

    /*
     * DO NOT PUBLISH THE KEY, SECRET AND CERT TO CODE REPOSITORIES
     * FOR SECURITY.
     */

    /*
     * The location of the VATSIM OAuth interface
     */
    'base' => env('SSO_URL'),

    /*
     * The consumer key for your organisation (provided by VATSIM)
     */
    'key' => env('SSO_KEY'),

    /*
     * The secret key for your organisation (provided by VATSIM)
     * Do not give this to anyone else or display it to your users. It must be kept server-side
     */
    'secret' => env('SSO_SECRET'),

    /*
     * The signing method you are using to encrypt your request signature.
     * Different options must be enabled on your account at VATSIM.
     * Options: RSA / HMAC
     */
    'method' => env('SSO_METHOD', 'RSA'),

    /*
     * Your RSA **PRIVATE** key
     * If you are not using RSA, this value can be anything (or not set)
     */
    'cert' => env('SSO_CERT'),

    /*
     * The URL users will be redirected to after they log in, this should
     * be on the same server as the request
     */
    'return' => env('SSO_RETURN'),

    'additionalConfig' => [
        'allow_suspended' => false,
        'allow_inactive' => false,
    ],

];

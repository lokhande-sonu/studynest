<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Application Token
    |--------------------------------------------------------------------------
    |
    | Shared secret the mobile app must send in the "Authorization" header
    | for every API request. Keep it long and random. When empty, all API
    | endpoints fail closed (401) so the API can never run unprotected.
    |
    */

    'token' => env('APP_API_TOKEN', ''),

];

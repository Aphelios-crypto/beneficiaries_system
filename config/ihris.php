<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stand-Alone Mode
    |--------------------------------------------------------------------------
    | When set to true, the application will NOT attempt to authenticate
    | against the iHRIS API. Local credentials will be used instead.
    */
    'stand_alone_mode' => filter_var(env('STAND_ALONE_MODE', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | iHRIS API Base URL
    |--------------------------------------------------------------------------
    | The base URL for the iHRIS REST API.
    */
    'api_base_url' => env('IHRIS_API_BASE_URL', 'https://ihris.bayambang.gov.ph/api'),

    /*
    |--------------------------------------------------------------------------
    | Login Endpoint
    |--------------------------------------------------------------------------
    | The endpoint (relative to api_base_url) used for authentication.
    */
    'login_endpoint' => env('IHRIS_LOGIN_ENDPOINT', '/login'),

    /*
    |--------------------------------------------------------------------------
    | Default API User Role
    |--------------------------------------------------------------------------
    | The default role assigned to users who authenticate via the iHRIS API.
    */
    'default_api_user_role' => env('IHRIS_DEFAULT_API_USER_ROLE', 'Employee'),
];

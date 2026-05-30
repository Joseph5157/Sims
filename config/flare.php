<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Flare API Key
    |--------------------------------------------------------------------------
    |
    | Only report to Flare in production. The APP_ENV guard here ensures
    | errors are never sent from local or CI environments even if FLARE_KEY
    | is accidentally set in a local .env file.
    |
    | Set FLARE_KEY in Railway's environment variables (not in .env.example).
    |
    */

    'key' => env('APP_ENV') === 'production' ? env('FLARE_KEY') : null,

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware enrich each error report sent to Flare. Remove any
    | you don't want; CensorRequestBodyFields is important for GDPR/privacy.
    |
    */

    'flare_middleware' => [
        \Spatie\LaravelFlare\FlareMiddleware\AddLogs::class,
        \Spatie\LaravelFlare\FlareMiddleware\AddEnvironmentInformation::class,
        \Spatie\LaravelFlare\FlareMiddleware\AddExceptionInformation::class,
        \Spatie\LaravelFlare\FlareMiddleware\AddDumps::class,
        \Spatie\LaravelFlare\FlareMiddleware\AddJobs::class,
        \Spatie\LaravelFlare\FlareMiddleware\AddContext::class,
        \Spatie\LaravelFlare\FlareMiddleware\AddQueries::class => [
            'maximum_number_of_collected_queries' => 200,
            'report_query_bindings' => true,
        ],
        \Spatie\LaravelFlare\FlareMiddleware\CensorRequestBodyFields::class => [
            'censor_fields' => ['password', 'password_confirmation', 'current_password'],
        ],
    ],

];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default number of items per page when not specified
    |
    */
    'default' => 15,

    /*
    |--------------------------------------------------------------------------
    | Per-Resource Pagination
    |--------------------------------------------------------------------------
    |
    | Specific pagination limits for different resources
    |
    */
    'certificates' => 20,
    'invoices' => 20,
    'students' => 20,
    'schools' => 15,
    'packages' => 15,
    'issuers' => 15,
    'templates' => 15,
    'classes' => 10,
    'events' => 15,

    /*
    |--------------------------------------------------------------------------
    | Dashboard Pagination
    |--------------------------------------------------------------------------
    |
    | Limits for dashboard widgets and recent items
    |
    */
    'dashboard' => [
        'recent_certificates' => 10,
        'recent_students' => 10,
        'recent_invoices' => 5,
    ],
];

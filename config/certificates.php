<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Prevent Duplicate Certificates
    |--------------------------------------------------------------------------
    |
    | When set to true, the system will prevent generating multiple certificates
    | for the same student and event combination. Set to false to allow
    | multiple certificates per student-event pair.
    |
    */
    'prevent_duplicate_per_event' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Monthly Certificate Limit
    |--------------------------------------------------------------------------
    |
    | Default number of certificates a school can issue per month on free plan
    |
    */
    'default_monthly_limit' => 10,

    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for certificate QR code generation
    |
    */
    'qr_code' => [
        'format' => 'png',
        'size' => 200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificate Type Options
    |--------------------------------------------------------------------------
    |
    | Available certificate types for issuance
    |
    */
    'types' => [
        'participation' => 'participation',
        'rank' => 'rank',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Rank Options
    |--------------------------------------------------------------------------
    |
    | Predefined rank options for rank-based certificates
    |
    */
    'rank_options' => [
        'Participation',
        '1st Place',
        '2nd Place',
        '3rd Place',
        'Winner',
        'Runner Up',
        'Excellence',
    ],
];

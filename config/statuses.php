<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Certificate Statuses
    |--------------------------------------------------------------------------
    |
    | Status values for certificates throughout the application
    |
    */
    'certificate' => [
        'pending' => 'pending',
        'approved' => 'approved',
        'rejected' => 'rejected',
    ],

    /*
    |--------------------------------------------------------------------------
    | School Statuses
    |--------------------------------------------------------------------------
    |
    | Status values for school approval and management
    |
    */
    'school' => [
        'pending' => 'pending',
        'approved' => 'approved',
        'rejected' => 'rejected',
        'suspended' => 'suspended',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Statuses
    |--------------------------------------------------------------------------
    |
    | Status values for invoice payment tracking
    |
    */
    'invoice' => [
        'pending' => 'pending',
        'paid' => 'paid',
        'overdue' => 'overdue',
    ],
];

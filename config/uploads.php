<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upload Disk
    |--------------------------------------------------------------------------
    |
    | The default disk to use for file uploads
    |
    */
    'disk' => env('UPLOAD_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Upload Paths
    |--------------------------------------------------------------------------
    |
    | Directory paths for different types of uploads
    |
    */
    'paths' => [
        'school_logos' => 'schools/logos',
        'certificate_logos' => 'schools/certificate_logos',
        'signatures' => 'schools/signatures',
        'schools' => 'schools',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Size Limits (in KB)
    |--------------------------------------------------------------------------
    |
    | Maximum file sizes for different upload types
    |
    */
    'max_sizes' => [
        'logo' => 2048, // 2MB
        'certificate_logo' => 2048, // 2MB
        'signature' => 1024, // 1MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Dimensions
    |--------------------------------------------------------------------------
    |
    | Minimum dimensions for image uploads
    |
    */
    'min_dimensions' => [
        'logo' => [
            'width' => 100,
            'height' => 100,
        ],
        'certificate_logo' => [
            'width' => 100,
            'height' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | MIME types allowed for uploads
    |
    */
    'allowed_types' => [
        'images' => ['jpeg', 'png', 'jpg', 'webp'],
        'signatures' => ['png', 'jpg', 'jpeg'],
    ],

    /*
    |--------------------------------------------------------------------------
    | School Upload Fields
    |--------------------------------------------------------------------------
    |
    | Fields that accept file uploads for schools
    |
    */
    'school_fields' => [
        'logo',
        'certificate_left_logo',
        'certificate_right_logo',
        'signature_left',
        'signature_middle',
        'signature_right',
    ],
];

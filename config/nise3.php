<?php

return [
    "is_dev_mode" => env("IS_DEVELOPMENT_MOOD", true),
    "should_ssl_verify" => env("IS_SSL_VERIFY", false),
    'nationalities' => [
        '1' => 'Bangladeshi',
        '2' => 'Indian',
        '3' => 'Pakistani',
        '4' => 'Spanish',
        '5' => 'English'
    ],
    'physical_disabilities' => [
        'VD' => [
            "title" => "Visual Disabilities",
            "title_en" => "Visual Disabilities",
            'code' => 'VD'
        ],
        'HD' => [
            "title" => "Hearing Disabilities",
            "title_en" => "Hearing Disabilities",
            'code' => 'HD'
        ],
        'MHD' => [
            "title" => "Mental Health Disabilities",
            "title_en" => "Mental Health Disabilities",
            'code' => 'MHD'
        ],
        'ID' => [
            "title" => "Intellectual Disabilities",
            "title_en" => "Intellectual Disabilities",
            'code' => 'ID'
        ],
        'SD' => [
            "title" => "Social Disabilities",
            "title_en" => "Social Disabilities",
            'code' => 'SD'
        ]
    ]
];

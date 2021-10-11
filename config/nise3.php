<?php

return [
    "is_dev_mode" => env("IS_DEVELOPMENT_MOOD", true),
    "should_ssl_verify" => env("IS_SSL_VERIFY", false),
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
    ],
    'nationalities' => [
        '1' => ['en' => 'Bangladeshi', 'bn' => 'Bangladeshi'],
        '2' => ['en' => 'Indian', 'bn' => 'Indian'],
        '3' => ['en' => 'Pakistani', 'bn' => 'Pakistani'],
        '4' => ['en' => 'Nepali', 'bn' => 'Nepali'],
    ],
    'education_levels' => [
        1 => ['en' => 'PSC/5 Pass', 'bn' => 'PSC/5 Pass'],
        2 => ['en' => 'JSC/JDC/8 Pass', 'bn' => 'JSC/JDC/8 Pass'],
        3 => ['en' => 'Secondary', 'bn' => 'Secondary'],
        4 => ['en' => 'Higher Secondary', 'bn' => 'Higher Secondary'],
        5 => ['en' => 'Diploma', 'bn' => 'Diploma'],
        6 => ['en' => 'Bachelor/Honors', 'bn' => 'Bachelor/Honors'],
        7 => ['en' => 'Masters', 'bn' => 'Masters'],
        8 => ['en' => 'PhD', 'bn' => 'PhD'],
    ],
    'exam_degree_results' => [
        '1' => ['en' => 'First Division/Class', 'bn' => 'First Division/Class'],
        '2' => ['en' => 'Second  Division/Class', 'bn' => 'Second  Division/Class'],
        '3' => ['en' => 'Third Division/Class', 'bn' => 'Third Division/Class'],
        '4' => ['en' => 'Grade', 'bn' => 'Grade'],
        '5' => ['en' => 'Appeared', 'bn' => 'Appeared'],
        '6' => ['en' => 'Enrolled', 'bn' => 'Enrolled'],
        '7' => ['en' => 'Awarded', 'bn' => 'Awarded'],
        '8' => ['en' => 'Do Not Mention', 'bn' => 'Do Not Mention'],
        '9' => ['en' => 'Pass', 'bn' => 'Pass'],
    ]
];

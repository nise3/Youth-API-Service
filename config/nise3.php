<?php

use App\Models\BaseModel;

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
    'relationship_types' => [
        '1' => "Father",
        '2' => "Mother",
        '3' => "Uncle",
        '4' => "Aunt",
        '5' => "Other",
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
        1=>[
            'id'=>1,
            'code'=>BaseModel::FIRST_DIVISION,
            'title_en' => 'First Division/Class',
            'title_bn' => 'First Division/Class'
        ],
        2=>[
            'id'=>2,
            'code'=>BaseModel::SECOND_DIVISION,
            'title_en' => 'Second  Division/Class',
            'title_bn' => 'Second  Division/Class'
        ],
        3=>[
            'id'=>3,
            'code'=>BaseModel::THIRD_DIVISION,
            'title_en' => 'Third Division/Class',
            'title_bn' => 'Third Division/Class'
        ],
        4=>[
            'id'=>4,
            'code'=>BaseModel::GRADE,
            'title_en' => 'Grade',
            'title_bn' => 'Grade'
        ],
        5=>[
            'id'=>5,
            'code'=>BaseModel::APPEARED,
            'title_en' => 'Appeared',
            'title_bn' => 'Appeared'
        ],
        6=>[
            'id'=>6,
            'code'=>BaseModel::ENROLLED,
            'title_en' => 'Enrolled',
            'title_bn' => 'Enrolled'
        ],
        7=>[
            'id'=>7,
            'code'=>BaseModel::AWARDED,
            'title_en' => 'Awarded',
            'title_bn' => 'Awarded'
        ],
        8=>[
            'id'=>8,
            'code'=>BaseModel::DO_NOT_MENTION,
            'title_en' => 'Do Not Mention',
            'title_bn' => 'Do Not Mention'
        ],
        9=>[
            'id'=>9,
            'code'=>BaseModel::PASS,
            'title_en' => 'Pass',
            'title_bn' => 'Pass'
        ],

    ]
];

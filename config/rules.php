<?php

return [

    "choice_number" => [
        'in' => [1,2,3,4],
    ],

    "lname" => [
        'min' => 3,
        'max' => 50,
    ],

    "ar_fname" => [
        'min' => 3,
        'max' => 50,
    ],

    "ar_lname" => [
        'min' => 3,
        'max' => 50,
    ],

    "bplace" => [
        'max' => 32,
    ],

    "bday" => [
        'max' => today()->subYear(4)->toDateString(),
        'min' => today()->subYear(96)->toDateString(),
    ],

    'email' => [
        'max' => 72,
    ],

    'phone' => [
        'max' => 15,
    ],

    'address' => [
        'max' => 90,
    ],

    'cni' => [
        'max' => 20,
    ],

    'profession' => [
        'max' => 32,
    ],

    'family_title' => [
        'max' => 32,
        'in' => ["father", "mother"],
    ],

    'sex' => [
        'in' => ['male', 'female'],
    ],

    // 'classroom_state'=>[

    // ],

    'capacity' => [
        'between' => '0,127',
    ]

];

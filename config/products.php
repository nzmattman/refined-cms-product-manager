<?php

return [
    'fields' => [
        'image' => [
            'imageNote' => 'Image here <em><strong>must be</strong> 770px wide x 400px tall</em>',
            'width' => 770,
            'height' => 400,
        ]
    ],

    'variations' => [
        'active' => true,
    ],

    'orders' => [
        'active' => true,
        'sms' => [
            'active' => true,
        ],
        'gst' => [
            'active' => true,
            'percent' => 10,
            'type' => 'inc' // inc or ex
        ],
        'currency' => 'AUD',
        'country_code' => 'AU',
        'timezone' => 'Australia/Adelaide',
    ],

    'cart' => [
        'image' => [
            'width' => 300,
            'height' => 300,
        ],
        'product_link' => [
            'active' => true,
            'base_url' => ''
        ],
        'extra_fees' => [
            // [
            //     'name' => 'Processing Fee',
            //     'percent' => 10
            // ],
            // [
            //     'name' => 'Fixed Fee',
            //     'value' => 25.99
            // ]
        ],
    ],

    'details_template_id' => '__PRODUCT_ID__',
];

<?php

return [
    'fields' => [
        'image' => [
            'imageNote' => 'Image here <em><strong>must be</strong> 770px wide x 400px tall</em>',
            'width' => 770,
            'height' => 400,
        ]
    ],

    'orders' => [
        'active' => true,
        'gst' => [
            'active' => true,
            'percent' => 10,
            'type' => 'inc' // inc or ex
        ]
    ],

    'details_template_id' => '__PRODUCT_ID__',
];

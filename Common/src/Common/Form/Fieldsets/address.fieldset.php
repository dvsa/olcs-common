<?php

return [
    'name' => 'address',
    'elements' => [
        'postcode' => [
            'type' => 'text',
            'label' => 'Postcode',
            'name' => 'postcode'
        ],
        'line_1' => [
            'type' => 'text',
            'label' => 'Address line 1',
            'name' => 'line_1'
        ],
        'line_2' => [
            'type' => 'text',
            'label' => 'Address line 2',
            'name' => 'line_2'
        ],
        'line_3' => [
            'type' => 'text',
            'label' => 'Address line 3',
            'name' => 'line_3'
        ],
        'line_4' => [
            'type' => 'text',
            'label' => 'Address line 4',
            'name' => 'line_4'
        ],
        'town' => [
            'type' => 'text',
            'label' => 'Town/City',
            'name' => 'town'
        ],
        'country' => [
            'type' => 'select',
            'label' => 'Country',
            'name' => 'country',
            'options' => [
            ]
        ]
    ]
];


<?php

return [
    'name' => 'address',
    'elements' => [
        'postcode' => [
            'type' => 'text',
            'label' => 'Postcode',
            'name' => 'postcode',
            'required' => true
        ],
        'line_1' => [
            'type' => 'text',
            'label' => 'Address line 1',
            'name' => 'line_1',
            'required' => true
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
            'name' => 'town',
            'required' => true
        ],
        'country' => [
            'type' => 'select',
            'label' => 'Country',
            'name' => 'country',
            'required' => true,
            'options' => [
            ]
        ]
    ]
];


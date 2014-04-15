<?php

return [
    'name' => 'address',
    'elements' => [
        'postcode' => [
            'type' => 'text',
            'label' => 'Postcode',
            'name' => 'postcode',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'line_1' => [
            'type' => 'text',
            'label' => 'Address line 1',
            'name' => 'line_1',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
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
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'country' => [
            'type' => 'select',
            'label' => 'Country',
            'name' => 'country',
            'required' => true,
            'value_options' => 'countries'
        ]
    ]
];


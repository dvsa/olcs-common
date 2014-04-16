<?php

return [
    'name' => 'address',
    'elements' => [
        'id' => [
            'type' => 'hidden'
        ],
        'postcode' => [
            'type' => 'text',
            'label' => 'Postcode',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'addressLine1' => [
            'type' => 'text',
            'label' => 'Address line 1',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'addressLine2' => [
            'type' => 'text',
            'label' => 'Address line 2'
        ],
        'addressLine3' => [
            'type' => 'text',
            'label' => 'Address line 3'
        ],
        'addressLine4' => [
            'type' => 'text',
            'label' => 'Address line 4'
        ],
        'city' => [
            'type' => 'text',
            'label' => 'Town/City',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'country' => [
            'type' => 'select',
            'label' => 'Country',
            'required' => true,
            'value_options' => 'countries'
        ]
    ]
];

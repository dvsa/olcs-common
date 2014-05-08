<?php

return [
    'name' => 'address',
    'elements' => [
        'id' => [
            'type' => 'hidden'
        ],
        'version' => [
            'type' => 'hidden'
        ],
        'postcodeLookup' => [
            'type' => 'postcode-search'
        ],
        'addressLine1' => [
            'type' => 'text',
            'label' => 'Street',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'addressLine2' => [
            'type' => 'text',
            'label' => 'Address line 2',
            'label_attributes' => [
                'class' => 'visually-hidden',
            ],
        ],
        'addressLine3' => [
            'type' => 'text',
            'label' => 'Address line 3',
            'label_attributes' => [
                'class' => 'visually-hidden',
            ],
        ],
        'addressLine4' => [
            'type' => 'text',
            'label' => 'Address line 4',
            'label_attributes' => [
                'class' => 'visually-hidden',
            ],
        ],
        'city' => [
            'type' => 'text',
            'label' => 'Town/City',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'postcode' => [
            'type' => 'text',
            'label' => 'Postcode',
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

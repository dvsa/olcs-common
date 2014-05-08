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
        'searchPostcode' => [
            'type' => 'postcode-search',
            'label' => 'Postcode search'
        ],
        'addressLine1' => [
            'type' => 'text',
            'label' => 'Street',
            'class' => 'long',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'addressLine2' => [
            'type' => 'text',
            'label' => 'Address line 2',
            'class' => 'long',
            'label_attributes' => [
                'class' => 'visually-hidden',
            ],
        ],
        'addressLine3' => [
            'type' => 'text',
            'label' => 'Address line 3',
            'class' => 'long',
            'label_attributes' => [
                'class' => 'visually-hidden',
            ],
        ],
        'addressLine4' => [
            'type' => 'text',
            'label' => 'Address line 4',
            'class' => 'long',
            'label_attributes' => [
                'class' => 'visually-hidden',
            ],
        ],
        'city' => [
            'type' => 'text',
            'label' => 'Town/City',
            'class' => 'long',
            'filters' => '\Common\Form\Elements\InputFilters\TextRequired'
        ],
        'postcode' => [
            'type' => 'text',
            'label' => 'Postcode',
            'class' => 'long',
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

<?php

$translationPrefix = 'application_your-business';
return [
    'name' => 'business-type',
    'elements' =>
    [
        'business-type' =>
        [
            'name' => 'business-type',
            'label' => $translationPrefix . '_business-type.data.organisationType',
            'type' => 'select',
            'attributes' => array(
                'id' => 'business-type',
                'class' => '',
            ),
            'value_options' => 'business_types',
        ]
    ],
    'options' =>
    [
        'label' => 'business-type',
        'next_step' =>
        [
            'default' => 'details',
        ]
    ]
];

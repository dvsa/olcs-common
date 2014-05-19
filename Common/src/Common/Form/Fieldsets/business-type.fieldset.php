<?php

return [
    'name' => 'business-type',
    'elements' =>
    [
        'business-type' =>
        [
            'name' => 'business-type',
            'label' => 'What type of business are you?',
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
        'label' => 'Business type',
        'next_step' =>
        [
            'default' => 'details',
        ]
    ]
];

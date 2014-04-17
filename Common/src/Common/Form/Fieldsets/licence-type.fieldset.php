<?php

return [
    'name' => 'licence-type',
    'elements' => [
        'licence_type' => [
            'type' => 'radio',
            'label' => 'What type of licence do you want to apply for?',
            'value_options' => 'licence_types',
            'options' => [
                'label' => 'Licence Type',
                'next_step' =>
                [
                    'goods' => 'licence-type',
                    'psv' => 'licence-type-psv'
                ]
            ],
        ]
    ],
    'options' =>
    [
        'label' => 'Licence type',
        'next_step' =>
        [
            'values' => [
                'restricted' => 'complete',
                'standard-national' => 'complete',
                'standard-international' => 'complete',
                'special-restricted' => 'complete',
            ],
            'default' => 'complete',
        ]
    ]
];

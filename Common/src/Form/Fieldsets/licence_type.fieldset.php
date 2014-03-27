<?php

return 
[    
    'name' => 'licence_type',
    'options' => [
        'label' => 'Licence Type',
    ],
    'elements' => [
        'licence_type' => [
            'type' => 'radio',
            'label' => 'What type of licence do you want to apply for?',
            'value_options' => 'licence_types',
            'options' => [
                'label' => 'Licence Type',
                'next_step' => 
                [
                    'goods' => 'licence_type',
                    'psv' => 'licence_type_psv'
                ]
            ],
        ]
    ]
];
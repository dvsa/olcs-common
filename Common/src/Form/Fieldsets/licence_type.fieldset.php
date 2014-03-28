<?php

return 
[    
    'name' => 'licence_type',
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
    ],
    'options' => 
    [
        'label' => 'Licence Type',
        'next_step' => 
        [
            'restricted' => 'complete',
            'standard-national' => 'complete',
            'standard-international' => 'complete',
            'special-restricted' => 'complete',
        ]
    ],
];
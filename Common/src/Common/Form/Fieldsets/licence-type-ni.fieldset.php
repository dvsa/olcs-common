<?php

return 
[    
    'name' => 'licence-type-ni',
    'elements' => 
    [
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
        ],
        'ni_flag' => [
            'name' => 'ni_flag',
            'type' => 'hidden',
            'attributes' => [
                'value' => '1',
            ],
        ],
    ],
    'options' =>
    [
        'next_step' =>
        [
            'default' => 'complete',
        ],
    ],
];


<?php

return 
[    
    'name' => 'licence_type_psv',
    'elements' => 
    [
        'licence_type_psv' => 
        [
            'name' => 'licence_type_psv',
            'type' => 'radio',
            'label' => 'What type of licence do you want to apply for?',
            'value_options' => 'licence_types_psv',
            'attributes' => 
            [
                'id' => 'operator_location',
                'class' => '',
            ],
            'value_options' => 'licence_types_psv',
        ],
    ],
    'options' => 
    [
        'label' => 'PSV Licence Type',
        'next_step' => 
        [
            'restricted' => 'complete',
            'standard-national' => 'complete',
            'standard-international' => 'complete',
            'special-restricted' => 'complete',
        ]
    ],
];


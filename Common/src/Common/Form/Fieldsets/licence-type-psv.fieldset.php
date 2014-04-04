<?php

return 
[    
    'name' => 'licence-type-psv',
    'elements' => 
    [
        'licence-type-psv' => 
        [
            'name' => 'licence-type-psv',
            'type' => 'radio',
            'label' => 'What type of licence do you want to apply for?',
            'value_options' => 'licence_types_psv',
            'attributes' => 
            [
                'id' => 'licence-type-psv',
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
        ], 
        'final_step' => true
    ],
];


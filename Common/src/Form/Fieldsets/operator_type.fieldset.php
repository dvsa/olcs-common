<?php
return
[    
    'name' => 'operator_type',
    'elements' => 
    [
        'operator_type' => 
        [
            'name' => 'operator_type',
            'label' => 'What type of operator are you?',
            'type' => 'radio',
            'attributes' => array(
                'id' => 'operator_location',
                'class' => '',
            ),
            'value_options' => 'operator_types',
              
        ]
    ],
    'options' => 
    [
        'label' => 'Operator Type',
        'next_step' => 
        [
            'goods' => 'licence_type',
            'psv' => 'licence_type_psv'
        ]
    ],
];
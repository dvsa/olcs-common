<?php
return
[    
    'name' => 'operator-type',
    'elements' => 
    [
        'operator-type' => 
        [
            'name' => 'operator-type',
            'label' => 'What type of operator are you?',
            'type' => 'radio',
            'attributes' => array(
                'id' => 'operator-location',
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
            'values' => [
                'goods' => 'licence-type',
                'psv' => 'licence-type-psv',
            ],
            'default' => 'complete',
        ]
    ],
];
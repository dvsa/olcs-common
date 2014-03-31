<?php
return
[    
    'name' => 'business_type',
    'elements' => 
    [
        'business_type' => 
        [
            'name' => 'business_type',
            'label' => 'What type of business are you?',
            'type' => 'select',
            'attributes' => array(
                'id' => 'business_type',
                'class' => '',
            ),
            'value_options' => 'business_types',
              
        ]
    ],
    'options' => 
    [
        'label' => 'Business Type',
        'next_step' => 
        [
            'goods' => 'licence_type',
            'psv' => 'licence_type_psv'
        ]
    ],
];
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
            'org_type.rc' => 'registered_company',
            'org_type.st' => 'sole_trader',
            'org_type.p' => 'partnership',
            'org_type.llp' => 'llp',
            'org_type.pa' => 'public_authority'
        ]
    ],
];
<?php
return
[    
    'name' => 'registered_company',
    'elements' => 
    [
        'trading_names' => 
        [
            'name' => 'trading_names[]',
            'label' => 'Trading names',
            'type' => 'text',
            'attributes' => array(
                'id' => 'trading_names[]',
                'class' => '',
            )              
        ],
        'submit_add_trading_name' => 
        [
            'name' => 'submit_add_trading_name',
            'type' => 'submit',
            'label' => 'Add another',
            'attributes' => array(
                'id' => 'submit_add_trading_name',
                'class' => '',
            ),
            
        ],
        'type_of_business' => 
        [
            'name' => 'business_type',
            'label' => 'Select your business types from the list below',
            'type' => 'select',
            'attributes' => array(
                'id' => 'business_type',
                'class' => '',
            ),
            'value_options' => 'sic_codes',             
        ],
    ],
    'options' => 
    [
        'label' => 'Business Type',
        'final_step' => 1,
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

<?php
return
[    
    'name' => 'sole_trader',
    'elements' => 
    [
        'trading_names' => 
        [
            'type' => 'tradingNames',              
        ],
        'submit_add_trading_name' => 
        [
            'name' => 'submit_add_trading_name',
            'type' => 'addAnotherButton',
        ],
        'type_of_business' => 
        [
            'type' => 'businessType',
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

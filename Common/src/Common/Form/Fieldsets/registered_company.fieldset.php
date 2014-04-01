<?php
return
[    
    'name' => 'registered_company',
    'elements' => 
    [
        'company_number' => 
        [
            'label' => 'Registered company number',
            'type' => 'companyNumber',
        ],
        'submit_lookup_company' => 
        [
            'name' => 'submit_lookup_company',
            'value' => 'lookup_company',
            'type' => 'findButton'
        ],
        'company_name' => 
        [
            'type' => 'companyName',
        ],
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
        'final_step' => 1,
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
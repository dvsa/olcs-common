<?php
return
[    
    'name' => 'llp',
    'elements' => 
    [
        'company_number' => 
        [
            'label' => 'Registered company number',
            'type' => 'companyNumber',
        ],
        'submit_lookup_company' => 
        [
            'type' => 'findButton'
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
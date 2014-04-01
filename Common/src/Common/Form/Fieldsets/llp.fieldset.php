<?php
return
[    
    'name' => 'llp',
    'elements' => 
    [
        'company_number' => 
        [
            'name' => 'company_number',
            'label' => 'LLP number:',
            'type' => 'text',
            'attributes' => array(
                'id' => 'company_number',
                'class' => '',
            )              
        ],
        'submit_lookup_company' => 
        [
            'name' => 'submit_lookup_company',
            'type' => 'submit',
            'label' => 'Find',
            'attributes' => array(
                'id' => 'submit_lookup_company',
                'class' => '',
            )              
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
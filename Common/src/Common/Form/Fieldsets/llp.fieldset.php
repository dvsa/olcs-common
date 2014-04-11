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
            'name' => 'submit_lookup_company',
            'value' => 'lookup_company',
            'type' => 'submit',
            'label' => 'Find',
        ],
    ],
    'options' =>
    [
        'next_step' =>
        [
            'default' => 'complete',
        ]
    ],
];
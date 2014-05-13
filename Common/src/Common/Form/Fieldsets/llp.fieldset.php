<?php

return [
    'name' => 'llp',
    'elements' =>
    [
        'business_type' => [
            'label' => 'What type of business are you?',
            'type' => 'selectDisabled',
        ],
        'company_number' =>
        [
            'label' => 'Registered company number',
            'type' => 'companyNumber'
        ],
        'submit_lookup_company' =>
        [
            'name' => 'submit_lookup_company',
            'value' => 'lookup_company',
            'type' => 'submit',
            'label' => 'Find'
        ],
        'company_name' =>
        [
            'type' => 'companyName'
        ],
    ],
    'options' =>
    [
        'next_step' =>
        [
            'default' => 'complete'
        ]
    ]
];

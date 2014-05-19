<?php

return [
    'name' => 'registered-company',
    'elements' =>
    [
        'business_type' => [
            'label' => 'What type of business are you?',
            'type' => 'selectDisabled',
            'value_options' => 'business_types',
            'class' => 'inline',
        ],
        'edit_business_type' => [
            'type' => 'submit',
            'label' => 'Edit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionLink'
        ],
        'company_number' =>
        [
            'type' => 'companyNumber',
            'label' => 'Registered company number',
        ],
        'company_name' =>
        [
            'type' => 'companyName',
            'label' => 'Company name',
        ],
        'trading_names' =>
        [
            'label' => 'Trading names (optional)',
            'type' => 'tradingNames'
        ],
    ],
    'options' =>
    [
        'next_step' =>
        [
            'default' => 'addresses'
        ]
    ]
];

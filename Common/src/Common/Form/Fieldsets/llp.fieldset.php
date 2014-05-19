<?php

return [
    'name' => 'llp',
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
            'label' => 'Registered company number',
            'type' => 'companyNumber',
        ],
        'company_name' =>
        [
            'type' => 'companyName'
        ],
        'trading_names' =>
        [
            'label' => 'Trading names (optional)',
            'type' => 'tradingNames'
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
            'default' => 'addresses'
        ]
    ]
];

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
            'label' => 'Registered company number',
            'type' => 'companyNumber',
        ],
        'company_name' =>
        [
            'type' => 'companyName'
        ],
        'type_of_business' =>
        [
            'type' => 'text',
            'label' => 'Nature of business (SIC)'
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
            'default' => 'complete'
        ]
    ]
];

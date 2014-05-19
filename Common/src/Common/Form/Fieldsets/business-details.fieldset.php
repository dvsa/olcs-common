<?php

$translationPrefix = 'application_your-business';
$detailsPrefix = $translationPrefix .  '_business-details.data.';
return [
    'name' => 'business-details',
    'elements' =>
    [
        'business_type' => [
            'label' => $translationPrefix . '_business-type.data.organisationType',
            'type' => 'selectDisabled',
            'value_options' => 'business_types',
            'class' => 'inline',
        ],
        'edit_business_type' => [
            'type' => 'submit',
            'label' => 'edit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionLink'
        ],
        'company_number' =>
        [
            'type' => 'companyNumber',
            'label' => $detailsPrefix . 'company_number',
        ],
        'company_name' =>
        [
            'type' => 'companyName',
            'label' => $detailsPrefix . 'company_name',
        ],
        'trading_names' =>
        [
            'label' => $detailsPrefix . 'trading_names_optional',
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

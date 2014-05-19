<?php

return [
    'name' => 'other',
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
        'company_name' =>
        [
            'type' => 'companyName',
            'label' => 'Organisation name',
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

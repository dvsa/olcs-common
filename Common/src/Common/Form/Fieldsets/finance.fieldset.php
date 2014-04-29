<?php

return [
    'name' => 'finance',
    'elements' => [
        'bankrupt' => [
            'type' => 'radio',
            'label' => '1. Declared bankrupt or had their estate seized or confiscated?',
            'value_options' => 'yes_no',
        ],
        'liquidation' => [
            'type' => 'radio',
            'label' => '2. Involved with a company, or business, that has gone into (or is going into) liquidation, owing money?',
            'value_options' => 'yes_no',
        ],
        'receivership' => [
            'type' => 'radio',
            'label' => '3. Involved with a company, or business, that has gone into (or is going into) receivership?',
            'value_options' => 'yes_no',
        ],
        'administration' => [
            'type' => 'radio',
            'label' => '4. Involved with a company, or business, that has gone into (or is going into) administration?',
            'value_options' => 'yes_no',
        ],
        'disqualified' => [
            'type' => 'radio',
            'label' => '5. Have you, or have any of your partners, directors, majority shareholders or your transport manager ever been disqualified from acting as a director of a company or from taking part in the management of a company?',
            'value_options' => 'yes_no',
        ],
        'insolvencyDetails' => [
            'type' => 'financialHistoryTextarea',
            'label' => 'Additional information',
            'description' => 'Please provide additional information relating to any prior insolvency proceedings. You may also upload evidence such as legal documents.',
            'placeholder' => 'Min 200 characters',
        ],
        'insolvencyConfirmation' => [
            'type' => 'checkbox',
            'label' => 'Please tick to confirm that you are aware that you must tell the traffic commissioner immediately of any insolvency proceedings that occur between the submission of your application and a decision being made on the application',
            'options' => [
                'must_be_checked' => true,
                'not_checked_message' => 'You must confirm that you have enough parking spaces',
            ],
        ],
    ],
    'options' =>
    [
        'next_step' =>
        [
            'default' => 'licence',
        ],
        'label' => 'Financial history',
    ]
];

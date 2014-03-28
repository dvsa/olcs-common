<?php

return [
    'case' => [
        'name' => 'case',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [
            [
                'name' => 'categories',
                'options' => [
                    'label' => 'Select one or more categories'
                ],
                'elements' => [
                    'compliance' => [
                        'type' => 'multicheckbox',
                        'label' => 'Compliance',
                        'value_options' => 'case_categories_compliance'
                    ],
                    /*'bus' => [
                        'type' => 'multicheckbox',
                        'label' => 'Bus registration',
                        'value_options' => 'case_categories_bus'
                    ],*/
                    'tm' => [
                        'type' => 'multicheckbox',
                        'label' => 'TM',
                        'value_options' => 'case_categories_tm'
                    ],
                    'app' => [
                        'type' => 'multicheckbox',
                        'label' => 'Licensing application',
                        'value_options' => 'case_categories_app'
                    ],
                    'referral' => [
                        'type' => 'multicheckbox',
                        'label' => 'Licence referral',
                        'value_options' => 'case_categories_referral'
                    ]
                ]
            ],
            [
                'name' => 'fields',
                'options' => [
                    'label' => 'Info'
                ],
                'elements' => [
                    'summary' => [
                        'type' => 'textarea',
                        'label' => 'Case summary'
                    ],
                    'ecms' => [
                        'type' => 'text',
                        'label' => 'ECMS #'
                    ]
                ]
            ]
        ],
        'elements' => [
            'licence' => [
                'type' => 'hidden'
            ],
            'crsf' => [
                'type' => 'crsf',
            ],
            'submit' => [
                'type' => 'submit',
                'label' => 'Save'
            ]
        ]
    ]
];


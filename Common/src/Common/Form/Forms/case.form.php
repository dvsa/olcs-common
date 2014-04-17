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
                        'value_options' => 'case_categories_compliance',
                        'required' => false
                    ],
                    /*'bus' => [
                        'type' => 'multicheckbox',
                        'label' => 'Bus registration',
                        'value_options' => 'case_categories_bus',
                        'required' => false
                    ],*/
                    'tm' => [
                        'type' => 'multicheckbox',
                        'label' => 'TM',
                        'value_options' => 'case_categories_tm',
                        'required' => false
                    ],
                    'app' => [
                        'type' => 'multicheckbox',
                        'label' => 'Licensing application',
                        'value_options' => 'case_categories_app',
                        'required' => false
                    ],
                    'referral' => [
                        'type' => 'multicheckbox',
                        'label' => 'Licence referral',
                        'value_options' => 'case_categories_referral',
                        'required' => false
                    ]
                ]
            ],
            [
                'name' => 'fields',
                'options' => [
                    'label' => 'Info'
                ],
                'elements' => [
                    'description' => [
                        'type' => 'textarea',
                        'label' => 'Case summary',
                        'class' => 'extra-long'
                    ],
                    'ecms' => [
                        'type' => 'text',
                        'label' => 'ECMS #',
                        'class' => 'medium'
                    ]
                ]
            ]
        ],
        'elements' => [
            'licence' => [
                'type' => 'hidden'
            ],
            'id' => [
                'type' => 'hidden'
            ],
            'version' => [
                'type' => 'hidden'
            ],
            'crsf' => [
                'type' => 'crsf',
            ],
            'submit' => [
                'type' => 'submit',
                'label' => 'Save',
                'class' => 'action--primary large'
            ]
        ]
    ]
];

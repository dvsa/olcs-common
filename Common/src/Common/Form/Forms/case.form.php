<?php

return [
    'case' => [
        'name' => 'case',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [
            [
                'name' => 'submissionSections',
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
                    ],
                    'bus' => [
                        'type' => 'multicheckbox',
                        'label' => 'Bus registration',
                        'value_options' => 'case_categories_bus',
                        'required' => false
                    ],
                ]
            ],
            [
                'name' => 'fields',
                'options' => [
                ],
                'elements' => [
                    'description' => [
                        'type' => 'textarea',
                        'label' => 'Case summary',
                        'class' => 'extra-long'
                    ],
                    'ecmsNo' => [
                        'type' => 'text',
                        'label' => 'ECMS number',
                        'class' => 'medium'
                    ],
                    'licence' => [
                        'type' => 'hidden'
                    ],
                    'id' => [
                        'type' => 'hidden'
                    ],
                    'version' => [
                        'type' => 'hidden'
                    ],
                ]
            ],
            [
                'name' => 'form-actions',
                'attributes' => [
                    'class' => 'actions-container'
                ],
                'elements' => [
                    'submit' => [
                        'enable' => true,
                        'type' => 'submit',
                        'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
                        'label' => 'Save',
                        'class' => 'action--primary large'
                    ],
                    'cancel' => array(
                        'enable' => true,
                        'type' => 'submit',
                        'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
                        'label' => 'Cancel',
                        'class' => 'action--secondary large'
                    )
                ]
            ]
        ]
    ]
];

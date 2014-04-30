<?php

return [
    'case-stay' => [
        'name' => 'case-stay',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [
            [
                'name' => 'fields',
                'elements' => [
                    'requestDate' => [
                        'type' => 'dateSelect',
                        'label' => 'Date of request',
                        'class' => 'extra-long',
                        'options' => array(
                            'min_year' => 2005
                        )
                    ],
                    'outcome' => [
                        'type' => 'select',
                        'label' => 'Outcome',
                        'value_options' => 'case_stay_outcome'
                    ],
                    'notes' => [
                        'type' => 'textarea',
                        'label' => 'Notes',
                        'filters' => '\Common\Form\Elements\InputFilters\TextMax4000Required',
                        'class' => 'extra-long'
                    ],
                ]
            ]
        ],
        'elements' => [
            'licence' => [
                'type' => 'hidden'
            ],
            'case' => [
                'type' => 'hidden'
            ],
            'stayType' => [
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

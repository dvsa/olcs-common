<?php

return [
    'case-stay' => [
        'name' => 'case-stay',
        'attributes' => [
            'method' => 'post',
        ],
        'type' => 'Common\Form\Form',
        'fieldsets' => [
            [
                'name' => 'fields',
                'elements' => [
                    'requestDate' => [
                        'type' => 'dateSelectWithEmpty',
                        'label' => 'Date of request',
                        'class' => 'extra-long',
                        'filters' => '\Common\Form\Elements\InputFilters\DateRequired',
                    ],
                    'outcome' => [
                        'type' => 'select',
                        'label' => 'Outcome',
                        'value_options' => 'case_stay_outcome',
                        'filters' => '\Common\Form\Elements\InputFilters\SelectEmpty'
                    ],
                    'notes' => [
                        'type' => 'textarea',
                        'label' => 'Notes',
                        'filters' => '\Common\Form\Elements\InputFilters\TextMax4000',
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
            'submit' => [
                'type' => 'submit',
                'label' => 'Save',
                'class' => 'action--primary large'
            ]
        ]
    ]
];

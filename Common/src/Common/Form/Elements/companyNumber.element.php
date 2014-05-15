<?php

return [
    'companyNumber' => [
        'type' => 'fieldset',
        'name' => 'company_number',
        'attributes' => [
            'class' => 'highlight-box',
        ],
        'elements' => [
            [
                'spec' => [
                    'type' => 'text',
                    'name' => 'company_number',
                    'attributes' => [
                        'class' => 'short',
                        'data-container-class' => 'inline'
                    ],
                ]
            ],
            [
                'spec' => [
                    'type' => 'button',
                    'name' => 'submit_lookup_company',
                    'options' => [
                        'label' => 'Find company',
                    ],
                    'attributes' => [
                        'class' => 'action--secondary large',
                        'data-container-class' => 'inline',
                        'type' => 'submit',
                    ],
                ]
            ],
            [
                'spec' => [
                    'type' => 'Common\Form\Elements\Types\PlainText',
                    'name' => 'description',
                    'options' => [
                        'value' => 'selfserve-business-registered-company-description'
                    ]
                ],
            ]
        ]
    ]
];

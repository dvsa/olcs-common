<?php
return [
    'search' => [
        'name' => 'search',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [
            [
                'name' => 'textelements',
                'options' => [
                    'label' => 'Fieldset 1'
                ],
                'elements' => [
                    'lic' => [
                        'type' => 'text',
                        'label' => 'Lic #',
                        'placeholder' => 'Licence number'
                    ],
                    'tradingName' => [
                        'type' => 'text',
                        'label' => 'Operator / trading name',
                        'placeholder' => 'Trading name'
                    ],
                    'select 1' => [
                        'type' => 'select',
                        'label' => 'Select 1',
                        'value_options' => 'bus_trc_status'
                    ],
                    /*'dateselect 1' => [
                        'type' => 'dateSelect',
                        'label' => 'Date of Birth',
                    ],*/
                    'postcode' => [
                        'type' => 'text',
                        'label' => 'Postcode',
                        'placeholder' => 'Postcode'
                    ],
                    'firstname' => [
                        'type' => 'personName',
                        'label' => 'First name'
                    ],
                    'lastname' => [
                        'type' => 'personName',
                        'label' => 'Last name'
                    ],
                ]
            ]
        ],
        'elements' => [
            'crsf' => [
                'type' => 'crsf',
            ],
            'submit' => [
                'type' => 'submit',
                'label' => 'Submit'
            ]
        ]
    ]
];


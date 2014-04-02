<?php

return [
    'search' => [
        'name' => 'search',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [
            [
                'name' => 'search',
                'options' => [
                    0
                ],
                'elements' => [
                    'licenceNumber' => [
                        'type' => 'text',
                        'label' => 'Lic #',
                        'attributes' => ['class'=>'long'],
                        'placeholder' => 'Licence number'
                    ],
                    'operatorName' => [
                        'type' => 'text',
                        'label' => 'Operator / trading name',
                        'placeholder' => 'Trading name'
                    ],
                    'postcode' => [
                        'type' => 'text',
                        'label' => 'Postcode',
                        'placeholder' => 'Postcode'
                    ],
                    'firstName' => [
                        'type' => 'personName',
                        'label' => 'First name',
                        'placeholder' => 'First name'
                    ],
                    'lastName' => [
                        'type' => 'personName',
                        'label' => 'Last name',
                        'placeholder' => 'Last name'
                    ]/*,
                    'dateOfBirth' => [
                        'type' => 'dateSelect',
                        'label' => 'Date of birth'
                    ]*/
                ]
            ],
            [
                'name' => 'advanced',
                'options' => [
                    'label' => 'Advanced Search'
                ],
                'elements' => [
                    'address' => [
                        'type' => 'textarea',
                        'label' => 'Address'
                    ],
                    'town' => [
                        'type' => 'text',
                        'label' => 'Town'
                    ],
                    'caseNumber' => [
                        'type' => 'text',
                        'label' => 'Case Number'
                    ],
                    'transportManagerId' => [
                        'type' => 'text',
                        'label' => 'Transport Manager ID'
                    ],
                    'operatorId' => [
                        'type' => 'text',
                        'label' => 'Operator ID'
                    ],
                    'vehicleRegMark' => [
                        'type' => 'text',
                        'label' => 'Vehicle Registration Mark'
                    ],
                    'diskSerialNumber' => [
                        'type' => 'text',
                        'label' => 'Disk Serial Number'
                    ],
                    'fabsRef' => [
                        'type' => 'text',
                        'label' => 'Fabs Ref'
                    ],
                    'companyNo' => [
                        'type' => 'text',
                        'label' => 'Company No'
                    ]
                ]
            ]
        ],
        'elements' => [
            'submit' => [
                'type' => 'submit',
                'label' => 'Search',
            ]
        ]
    ]
];


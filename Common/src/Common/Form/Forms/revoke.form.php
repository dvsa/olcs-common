<?php

return [
    'revoke' => [
        'name' => 'revoke',
        'attributes' => [
            'method' => 'post',
        ],
        'type' => 'Common\Form\Form',
        'elements' => [
                'piReasons' => [
                    'type' => 'multiselect',
                    'label' => 'Select legislation',
                    'help-block' => 'Use CTRL to select multiple'
                ],
                'presidingTc' => [
                    'type' => 'select',
                    'label' => 'TC/DTC agreed',
                ],
                'ptrAgreedDate' => [
                    'type' => 'dateSelectWithEmpty',
                    'label' => 'PTR agreed date',
                    'filters' => '\Common\Form\Elements\InputFilters\DateRequired',
                ],
                'dateClosed' => [
                    'type' => 'dateSelectWithEmpty',
                    'label' => 'Closed date',
                ],
                'comment' => [
                    'type'  => 'textarea',
                    'label' => 'Notes',
                    'class' => 'extra-long'
                ],
            'case' => [
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

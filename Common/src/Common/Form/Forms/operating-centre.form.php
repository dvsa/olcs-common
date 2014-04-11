<?php
return [
    'operating-centre' => [
        'name' => 'authorised-vehicles',
        'attributes' => [
            'method' => 'post',
            
        ],
        'fieldsets' => [
            [
	            'name' => 'address',
	            'options' => [
	                'label' => 'Operating centre address',   
                ],
                'elements' => [],
            ],
            [
                'name' => 'authorised-vehicles',
                'options' => [
                    'label' => 'Authorised vehicles',
                ],
                'elements' => [
                    'no-of-vehicles' => [
	                    'type' => 'text',
	                    'label' => 'Total no. of vehicles',   
                    ],
                    'no-of-trailers' => [
                        'type' => 'text',
                        'label' => 'Total no. of trailers',
                    ],
                    'parking-spaces-confirmation' => [
	                    'type' => 'checkbox',
	                    'label' => 'Parking spaces confirmation',
                    ],
                    'permission-confirmation' => [
                        'type' => 'checkbox',
                        'label' => 'Permission confirmation',
                    ],
                ]
            ],
        ],
        'elements' => [
            'version' => [
                'type' => 'hidden',
            ],
            'submit' => [
                'type' => 'submit',
                'label' => 'Save'
            ],
        ]
    ]
];

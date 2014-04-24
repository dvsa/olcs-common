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
	                    'type' => 'vehiclesNumber',
	                    'label' => 'Total no. of vehicles',   
                    ],
                    'no-of-trailers' => [
                        'type' => 'vehiclesNumber',
                        'label' => 'Total no. of trailers',
                    ],
                    'parking-spaces-confirmation' => [
	                    'type' => 'checkbox',
	                    'label' => 'Parking spaces confirmation',
	                    'options' => [
	                        'must_be_checked' => true,
	                        'not_checked_message' => 'You must confirm that you have enough parking spaces',   
                        ],
                    ],
                    'permission-confirmation' => [
                        'type' => 'checkbox',
                        'label' => 'Permission confirmation',
                        'options' => [
                            'must_be_checked' => true,
                            'not_checked_message' => 'You must confirm that you have permission to use the premisses to park the number of vehicles & trailers stated',
                        ],
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

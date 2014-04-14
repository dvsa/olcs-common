<?php
return [
    'update-vehicle' => [
        'name' => 'update-vehicle',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [],
        
        'elements' => [
            'vrm' => [
                'label' => 'Vehicle Registration Number (VRM)',
                'type' => 'vehicleVrm',
            ],
            'plated_weight' => [
                'label' => 'Gross Plated Weight (Kg)',
                'type' => 'vehicleGPW',  
            ],
            'body_type' => [
                'type' => 'radio',
                'value_options' => 'vehicle_body_types',
                'options' => [
                    'label' => 'Body type:',
                ],
            ],
            'is_tipper' => [
                'type' => 'checkbox',
            ],
            'is_refrigerated' => [
                'type' => 'checkbox',
                
            ],
            'is_articulated' => [
                'type' => 'checkbox',
                
            ],
            
            
            
            'submit' => [
                'type' => 'submit',
                'label' => 'Next'
            ],
            'version' => [
	           'type' => 'hidden',
            ]
        ]
    ]
];


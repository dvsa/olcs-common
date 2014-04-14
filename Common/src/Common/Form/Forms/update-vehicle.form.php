<?php
return [
    'update-vehicle' => [
        'name' => 'update-vehicle',
        'attributes' => [
            'method' => 'post',
        ],
        
        'elements' => [
            'vehicle_id' => [
	           'type' => 'hidden',
            ],
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
                'label' => 'Tipper',
                'value' => 1,
                'required' => false
            ],
            'is_refrigerated' => [
                'type' => 'checkbox',
                'label' => 'Refrigerated',
                'value' => 1,
                'required' => false
            ],
            'is_articulated' => [
                'type' => 'checkbox',
                'label' => 'Articulated',
                'value' => 1,
                'required' => false
            ],
            'save-and-add-another' => [
                'type' => 'submit',
                'label' => 'Save and add another'
            ],
            'submit' => [
                'type' => 'submit',
                'label' => 'Save'
            ],
            'version' => [
	           'type' => 'hidden',
            ]
        ]
    ]
];


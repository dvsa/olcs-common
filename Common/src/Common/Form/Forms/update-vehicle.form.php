<?php
return [
    'update-vehicle' => [
        'name' => 'update-vehicle',
        'attributes' => [
            'method' => 'post',
        ],
        
        'elements' => [
            'id' => [
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
            //NOT PART OF THE STORY (2057)
            /*'body_type' => [
                'type' => 'radio',
                'value_options' => 'vehicle_body_types',
                'options' => [
                    'label' => 'Body type:',
                ],
            ],*/
            'submit_add_another' => [
                'name' => 'submit_add_another',
                'type' => 'submit',
                'label' => 'Save and add another'
            ],
            'submit' => [
                'name' => 'submit',
                'type' => 'submit',
                'label' => 'Save'
            ],
            'version' => [
	           'type' => 'hidden',
            ]
        ]
    ]
];


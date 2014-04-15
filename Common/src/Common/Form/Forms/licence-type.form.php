<?php
return [
    'licence-type' => [
        'name' => 'licence-type',
        'attributes' => [
            'method' => 'post',
        ],
        'fieldsets' => [],
        
        'elements' => [
            'submit' => [
                'type' => 'submit',
                'label' => 'Next',
                'class' => 'action--primary large'
            ],
            'version' => [
	           'type' => 'hidden',
            ]
        ]
    ]
];


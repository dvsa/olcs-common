<?php
return [
    'business-type' => [
        'name' => 'business-type',
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

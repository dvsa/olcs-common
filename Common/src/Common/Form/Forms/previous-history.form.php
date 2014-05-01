<?php

return [
    'previous-history' => [
        'name' => 'licence-type',
        'attributes' => [
            'method' => 'post',
        ],

        'fieldsets' => [],
        'elements' => [
            'submit' => [
                'type' => 'submit',
                'label' => 'Continue',
                'class' => 'action--primary large'
            ],
            'version' => [
                'type' => 'hidden',
            ]
        ]
    ]
];

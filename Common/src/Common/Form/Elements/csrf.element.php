<?php

return [
    'csrf' => [
        'type' => 'Zend\Form\Element\Csrf',
        'name' => 'security',
        'options' => [
            'csrf_options' => [
                'messageTemplates' => [
                    'notSame' => 'csrf-message'
                ],
                'timeout' => 600
            ]
        ]
    ]
];

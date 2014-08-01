<?php

return [
    'name' => 'defendant',
    'type' => '\Common\Form\Elements\Types\Person',
    'elements' => [
        'defType' => [
                        'type' => 'select',
                        'label' => 'Defendant type',
                        'value_options' => 'defendant_types'
                    ],
        'personType' => [
            'type' => 'submit',
            'class' => 'action--secondary large',
            'label' => 'Look up defendant',
            'attributes' => [
                'value' => 'Person type'
            ],
        ]
    ]
];

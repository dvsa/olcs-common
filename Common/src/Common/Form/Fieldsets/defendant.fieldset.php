<?php

return [
    'name' => 'defendant',
    'type' => '\Common\Form\Elements\Types\Defendant',
    'elements' => [
        'defType' => [
                        'type' => 'select',
                        'label' => 'Defendant type',
                        'value_options' => 'defendant_types'
                    ],
        'lookupTypeSubmit' => [
            'type' => 'submit',
            'class' => 'action--secondary small',
            'label' => 'Submit',
            'attributes' => [
                'value' => 'Lookup type'
            ],
        ]
    ]
];

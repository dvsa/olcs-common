<?php

return [
    'name' => 'defendant',
    'type' => '\Common\Form\Elements\Types\EntitySearch',
    'elements' => [
        'defendantType' => [
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

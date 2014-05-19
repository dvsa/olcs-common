<?php

return [
    'selectDisabled' => [
        'type' => '\Common\Form\Elements\InputFilters\SelectEmpty',
        'name' => '',
        'options' => [
            'label' => '',
            'value_options' => [],
            'value' => 'defendant_type.operator',
            'disable_inarray_validator' => false
        ],
        'attributes' => [
            'id' => '',
            'disabled' => true
        ],
        'required' => false,
    ]
];

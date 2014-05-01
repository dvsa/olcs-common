<?php

return [
    'dateSelect' => [
        'type' => 'Common\Form\Elements\Custom\DateSelect',
        'name' => '',
        'options' => [
            'label' => 'Date of Birth',
            'create_empty_option' => false,
            'render_delimiters' => 'd m y'
        ],
        'attributes' => [
            'id' => 'dob'
        ]
    ]
];

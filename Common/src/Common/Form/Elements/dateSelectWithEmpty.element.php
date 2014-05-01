<?php

return [
    'dateSelectWithEmpty' => [
        'type' => 'Common\Form\Elements\Custom\DateSelect',
        'name' => '',
        'options' => [
            'label' => 'Date of Birth',
            'create_empty_option' => true,
            'render_delimiters' => false,
            'required' => false,
        ],
        'attributes' => [
            'id' => 'dob'
        ]
    ]
];

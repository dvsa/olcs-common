<?php

return [
    'timeSelect' => [
        'type' => 'Common\Form\Elements\Custom\Time',
        'name' => '',
        'options' => [
            'label' => 'Time',
            'create_empty_option' => true,
            'required' => false,
            'format' => 'h:i'
        ],
        'attributes' => [
            'min' => '07:00',
            'max' => '19:00',
            'step' => '300'
        ]
    ]
];

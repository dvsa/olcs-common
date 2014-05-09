<?php

return [
    'timeSelect' => [
        'type' => '\Zend\Form\Element\Time',
        'name' => '',
        'options' => [
            'label' => 'Time',
            'create_empty_option' => true,
            'format' => 'h:i'
        ],
        'attributes' => [
            'min' => '07:00',
            'max' => '19:00',
            'step' => '300'
        ]
    ]
];

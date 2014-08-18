<?php

return [
    'reset' => [
        'type' => '\Zend\Form\Element\Button',
        'name' => 'reset',
        'options' => [
            'label' => 'Reset',
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-10',
        ],
        'attributes' => [
            'type' => 'reset',
            'class' => 'action--primary'
        ]
    ]
];

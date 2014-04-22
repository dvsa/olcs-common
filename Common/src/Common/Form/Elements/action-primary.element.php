<?php

return [
    'submit' => [
        'type' => '\Zend\Form\Element\Button',
        'name' => 'submit',
        'options' => [
            'label' => 'Submit',
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-10',
        ],
        'attributes' => [
            'type' => 'submit',
            'class' => 'action--primary'
        ]
    ]
];

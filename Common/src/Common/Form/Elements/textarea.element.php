<?php
return ['textarea' =>
        [
            'type' => '\Zend\Form\Element\Textarea',
            'name' => '',
            'options' => [
                'label' => '',
                'label_attributes' => ['class' => 'col-sm-2'],
                'column-size' => 'sm-6',
                'help-block' => 'You can type anything in this box.',
            ],
            'attributes' => [
                'id' => '',
            ],
            'filters' => [
                ['name' => 'Zend\Filter\StringTrim'],
                ['name' => 'Zend\Filter\StringToLower'],
            ],
            'validators' => [
                new \Zend\Validator\StringLength(['min' => 10, 'max' => 100]),

            ]
        ]
    ];


<?php

return [
    'tradingNames' => [
        'type' => 'fieldset',
        'options' => [
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-5',
            'help-block' => 'Between 2 and 50 characters.'
        ],
        'attributes' => [
            'id' => '',
            'placeholder' => ''
        ],
        'elements' => [
            [
                'spec' => [
                    'type' => 'Zend\Form\Element\Collection',
                    'name' => 'trading_name',
                    'options' => [
                        'count' => 1,
                        'wrapElements' => false,
                        'allow_add' => true,
                        'allow_remove' => true,
                        'target_element' => [
                            'type' => '\Common\Form\Fieldsets\Custom\TextFieldset',
                            'attributes' => [
                                'data-container-class' => 'block'
                            ],
                            'options' => [
                                'wrapElements' => false,
                            ]
                        ],
                    ],
                ],
            ],
            /**
             * NP 16/05/2014
             * Removed this as it was causing low-level zend errors during review.
             * Couldn't consult with the branch author (Jakub) due to him being on
             * holiday. Agreed with Stephen Liversedge to remove 'add another' from
             * AC of OLCS-2650 and merge as is, extracting 'add another' behaviour
             * out into a separate story
            [
                'spec' => [
                    'type' => 'button',
                    'name' => 'submit_add_trading_name',
                    'options' => [
                        'label' => 'Add another',
                    ],
                    'attributes' => [
                        'class' => 'action--secondary large',
                        'data-container-class' => 'inline',
                        'type' => 'submit',
                    ],
                ],
            ],
            */
        ],
    ],
];

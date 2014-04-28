<?php

return [
    'financialHistoryTextarea' => [
        'type' => '\Common\Form\Elements\InputFilters\FinancialHistoryTextarea',
        'name' => '',
        'options' => [
            'label' => '',
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-6',
            'help-block' => 'You can type anything in this box.'
        ],
        'attributes' => [
            'id' => ''
        ],
        'filters' => [
            ['name' => 'Zend\Filter\StringTrim'],
        ],
    ]
];

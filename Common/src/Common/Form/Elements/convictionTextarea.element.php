<?php

return [
    'convictionTextarea' => [
        'type' => '\Common\Form\Elements\InputFilters\ConvictionTextarea',
        'name' => 'offenceDetails',
        'options' => [
            'label' => 'selfserve-app-subSection-previous-history-criminal-conviction-offenseDetails',
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-6',
            'help-block' => 'selfserve-app-subSection-previous-history-criminal-conviction-helpBlock'
        ],
        'attributes' => [
            'id' => ''
        ],
        'filters' => [
            ['name' => 'Zend\Filter\StringTrim'],
        ],
    ]
];

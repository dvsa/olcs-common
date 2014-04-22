<?php

return [
    'companyName' => [
        'type' => '\Common\Form\Elements\InputFilters\Name',
        'name' => 'company_name',
        'options' => [
            'label' => 'Company name',
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-5',
            'help-block' => 'Between 2 and 50 characters.'
        ],
        'attributes' => [
            'id' => 'company_name',
            'placeholder' => ''
        ]
    ]
];

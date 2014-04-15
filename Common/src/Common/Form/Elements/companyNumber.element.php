<?php
return ['companyNumber' =>
            [
            'type' => '\Common\Form\Elements\InputFilters\CompanyNumber',
            'name' => 'company_number',
                'options' => 
                    [
                    'label' => '',
                    'label_attributes' => ['class' => 'col-sm-2'],
                    'column-size' => 'sm-5',
                    'help-block' => '8 numbers, e.g. 12345678',
                    ],
            'attributes' => 
                [
                'id' => 'company_number',
                'placeholder' => '',
                ],
            ]
        ];
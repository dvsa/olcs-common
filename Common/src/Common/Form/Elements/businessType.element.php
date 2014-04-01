<?php
return ['businessType' =>
            [
            'type' => 'select',
            'name' => 'business_type',
            'options' => [
                'label' => 'Select your business types from the list below',
                'label_attributes' => ['class' => 'col-sm-2'],
            ],
            'attributes' => [
                'id' => '',
                'placeholder' => '',
            ],
            'value_options' => 'sic_codes',    
        ]
];
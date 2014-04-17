<?php

// todo need a better way of doing this, for translator
$sic_codes = include __DIR__ . '/../../../../config/sic-codes/sicCodes_en_GB.php';

return [
    'businessType' => [
        'type' => 'select',
        'name' => 'business_type',
        'options' => [
            'label' => 'Select your business types from the list below',
            'label_attributes' => ['class' => 'col-sm-2'],
            'value_options' => $sic_codes
        ],
        'attributes' => [
            'id' => '',
            'placeholder' => ''
        ]
    ]
];

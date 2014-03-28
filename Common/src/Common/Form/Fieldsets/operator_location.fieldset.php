<?php
return             
[     
    'name' => 'operator_location',
    'elements' => 
    [
        'operator_location' => 
        [
            'name' => 'operator_location',
            'label' => 'Where do you operate from?',
            'type' => 'radio',
            'attributes' => 
            [
                'id' => 'operator_location',
                'class' => '',
            ],
            'value_options' => 'operator_locations',
        ]
    ],
    'options' => [
         'label' => 'Operator Location',
         'next_step' => 
         [
             'uk' => 'operator_type',
             'ni' => 'licence_type'
         ]
    ]
];
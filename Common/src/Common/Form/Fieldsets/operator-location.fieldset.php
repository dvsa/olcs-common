<?php
return             
[     
    'name' => 'operator_location',
    'elements' => 
    [
        'operator-location' => 
        [
            'name' => 'operator_location',
            'label' => 'Where do you operate from?',
            'type' => 'radio',
            'attributes' => 
            [
                'id' => 'operator-location',
                'class' => '',
            ],
            'value_options' => 'operator_locations',
        ]
    ],
    'options' => [
         'label' => 'Operator Location',
         'next_step' => 
         [
             'uk' => 'operator-type',
             'ni' => 'licence-type-ni'
         ]
    ]
];
<?php

return [
    'vehicleVrm' => [
        'type' => '\Common\Form\Elements\InputFilters\Vrm',
        'name' => 'vrm',
        'options' => [
            'label' => 'Vehicle Registration Mark (VRM)',
            'label_attributes' => ['class' => 'col-sm-2'],
            'column-size' => 'sm-5',
            'help-block' => 'Between 2 and 50 characters.',
        ],
        'attributes' => [
            'id' => 'vrm',
            'placeholder' => '',
        ]
    ]
];

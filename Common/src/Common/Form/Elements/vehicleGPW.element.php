<?php
return ['vehicleGPW' =>
            [
            'type' => '\Common\Form\Elements\InputFilters\Gpw',
            'name' => 'gross_plated_weight',
            'options' => [
                'label' => 'Gross Plated Weight',
                'label_attributes' => ['class' => 'col-sm-2'],
                'column-size' => 'sm-5',
                'help-block' => 'Between 2 and 50 characters.',
            ],
            'attributes' => [
                'id' => 'plated_weight',
                'placeholder' => '',
            ]
        ]
];
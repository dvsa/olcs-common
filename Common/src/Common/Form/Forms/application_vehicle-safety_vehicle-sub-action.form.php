<?php

$translationPrefix = 'application_vehicle-safety_vehicle-sub-action';

return array(
    $translationPrefix => array(
        'name' => $translationPrefix,
        'attributes' => array(
            'method' => 'post'
        ),
        'fieldsets' => array(
            array(
                'name' => 'data',
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden'
                    ),
                    'version' => array(
                        'type' => 'hidden'
                    ),
                    'vrm' => array(
                        'label' => $translationPrefix . '.data.vrm',
                        'type' => 'vehicleVrm'
                    ),
                    'platedWeight' => array(
                        'label' => $translationPrefix . '.data.weight',
                        'type' => 'vehicleGPW',
                        'filters' => '\Common\Form\Elements\InputFilters\VehicleWeight',
                    )
                )
            ),
            array(
                'name' => 'licence-vehicle',
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden'
                    ),
                    'version' => array(
                        'type' => 'hidden'
                    ),
                    'receivedDate' => array(
                        'label' => $translationPrefix . '.licence-vehicle.receivedDate',
                        'type' => 'dateSelectWithEmpty'
                    ),
                    'specifiedDate' => array(
                        'label' => $translationPrefix . '.licence-vehicle.specifiedDate',
                        'type' => 'dateSelectWithEmpty'
                    ),
                    'deletedDate' => array(
                        'label' => $translationPrefix . '.licence-vehicle.deletedDate',
                        'type' => 'dateSelectWithEmpty'
                    ),
                    'discNo' => array(
                        'label' => $translationPrefix . '.licence-vehicle.discNo',
                        'type' => 'text'
                    )
                )
            ),
            array(
                'type' => 'journey-crud-buttons'
            )
        )
    )
);

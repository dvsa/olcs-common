<?php

$translationPrefix = 'application_vehicle-safety_vehicle-psv-sub-action';

return array(
    $translationPrefix => array(
        'name' => $translationPrefix,
        'attributes' => array(
            'method' => 'post',
        ),
        'fieldsets' => array(
            array(
                'name' => 'data',
                'options' => array(),
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden'
                    ),
                    'version' => array(
                        'type' => 'hidden'
                    ),
                    'psvType' => array(
                        'type' => 'hidden'
                    ),
                    'vrm' => array(
                        'type' => 'vehicleVrm',
                        'label' => $translationPrefix. '.data.vrm'
                    ),
                    'isNovelty' => array(
                        'type' => 'yesNoRadio',
                        'label' => $translationPrefix. '.data.isNovelty'
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

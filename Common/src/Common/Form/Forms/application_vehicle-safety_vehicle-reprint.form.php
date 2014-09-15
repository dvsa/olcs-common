<?php

return array(
    'application_vehicle-safety_vehicle-reprint' => array(
        'name' => 'application_vehicle-safety_vehicle',
        'attributes' => array(
            'method' => 'post',
        ),
        'fieldsets' => array(
            array(
                'name' => 'data',
                'options' => array(),
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden',
                        'label' => 'vehicle-disc-reprint-confirm-label'
                    )
                )
            ),
            array(
                'type' => 'journey-delete-confirm-buttons'
            )
        )
    )
);

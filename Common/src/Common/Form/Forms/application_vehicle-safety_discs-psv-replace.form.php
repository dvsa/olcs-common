<?php

$translationPrefix = 'application_vehicle-safety_discs-psv-replace';

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
                        'type' => 'hidden',
                        'label' => $translationPrefix . '-label'
                    )
                )
            ),
            array(
                'type' => 'journey-confirm-buttons'
            )
        )
    )
);

<?php

$translationPrefix = 'application_vehicle-safety_discs-psv';

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
                    'validDiscs' => array(
                        'label' => $translationPrefix . '.validDiscs',
                        'type' => 'text'
                    ),
                    'pendingDiscs' => array(
                        'label' => $translationPrefix . '.pending',
                        'type' => 'text'
                    )
                )
            ),
            array(
                'name' => 'table',
                'options' => array(0),
                'type' => 'table'
            ),
            array(
                'type' => 'journey-buttons'
            )
        )
    )
);

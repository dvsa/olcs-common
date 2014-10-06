<?php

$translationPrefix = 'application_vehicle-safety_discs-psv-sub-action';

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
                    'totalAuth' => array(
                        'type' => 'hidden'
                    ),
                    'discCount' => array(
                        'type' => 'hidden'
                    ),
                    'additionalDiscs' => array(
                        'type' => 'text',
                        'label' => $translationPrefix . '.additionalDiscs',
                        'filters' => '\Common\Form\Elements\InputFilters\AdditionalPsvDiscs',
                    )
                )
            ),
            array(
                'type' => 'journey-crud-buttons'
            )
        )
    )
);

<?php

return array(
    'application_vehicle-safety_vehicle-delete' => array(
        'name' => 'application_vehicle-safety_vehicle-delete',
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
                        'label' => 'vehicle-remove-confirm-label'
                    )
                )
            ),
            array(
                'name' => 'form-actions',
                'alt-name' => 'journey-buttons-2',
                'attributes' => array(
                    'class' => 'actions-container'
                ),
                'options' => array(0),
                'elements' => array(
                    'submit' => array(
                        'enable' => true,
                        'type' => 'submit',
                        'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
                        'label' => 'Remove',
                        'class' => 'action--primary large'
                    ),
                    'back' => array(
                        'enable' => true,
                        'type' => 'submit',
                        'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
                        'label' => 'Cancel',
                        'class' => 'action--secondary large'
                    )
                )
            )
        )
    )
);

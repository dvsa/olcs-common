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
                        'label' => 'Are you sure you want to request replacement discs for the selected vehicle(s)?'
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
                        'label' => 'Ok',
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

<?php

return array(
    array(
        'name' => 'form-actions',
        'alt-name' => 'journey-delete-confirm-buttons',
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
);
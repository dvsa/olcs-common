<?php

return array(
    'name' => 'form-actions',
    'alt-name' => 'journey-crud-buttons',
    'attributes' => array(
        'class' => 'actions-container'
    ),
    'options' => array(0),
    'elements' => array(
        'submit' => array(
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Save',
            'class' => 'action--primary large'
        ),
        'addAnother' => array(
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Save & add another',
            'class' => 'action--primary large'
        ),
        'cancel' => array(
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Cancel',
            'class' => 'action--secondary large'
        )
    )
);

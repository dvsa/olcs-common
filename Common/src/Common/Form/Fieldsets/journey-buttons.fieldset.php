<?php

return array(
    'name' => 'form-actions',
    'attributes' => array(
        'class' => 'actions-container'
    ),
    'options' => array(0),
    'elements' => array(
        'submit' => array(
            'enable' => true,
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Continue',
            'class' => 'action--primary large'
        ),
        'back' => array(
            'enable' => true,
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Back',
            'class' => 'action--secondary large'
        ),
        'home' => array(
            'enable' => true,
            'type' => 'submit',
            'label' => 'Back to home',
            'filters' => '\Common\Form\Elements\InputFilters\ActionLink',
            'route' => 'home/dashboard',
            'class' => 'action--tertiary'
        )
    )
);

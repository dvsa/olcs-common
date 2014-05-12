<?php

return array(
    'name' => 'form-actions',
    'class' => 'action-buttons',
    'options' => array(0),
    'elements' => array(
        'submit' => array(
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Next',
            'class' => 'action--primary large'
        ),
        'back' => array(
            'type' => 'submit',
            'filters' => '\Common\Form\Elements\InputFilters\ActionButton',
            'label' => 'Back',
            'class' => 'action--secondary large'
        ),
        'home' => array(
            'type' => 'submit',
            'label' => 'Back to home',
            'filters' => '\Common\Form\Elements\InputFilters\ActionLink',
            'route' => 'selfserve/dashboard-home'
        )
    )
);

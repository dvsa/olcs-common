<?php

return array(
    'name' => 'table',
    'options' => array(0),
    'elements' => array(
        'table' => array(
            'type' => 'table',
            'label' => 'safety inspection provider',
            'filters' => '\Common\Form\Elements\InputFilters\TableRequired'
        ),
        'action' => array(
            'type' => 'hidden',
            'filters' => '\Common\Form\Elements\InputFilters\NoRender'
        ),
        'rows' => array(
            'type' => 'hidden'
        ),
        'id' => array(
            'type' => 'hidden',
            'filters' => '\Common\Form\Elements\InputFilters\NoRender'
        )
    )
);

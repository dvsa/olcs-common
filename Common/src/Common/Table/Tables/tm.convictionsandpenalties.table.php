<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.convictionsandpenalties.table',
        'empty_message' => 'transport-manager.convictionsandpenalties.table.empty',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-previous-conviction' => array('label' => 'Add', 'class' => 'primary'),
                'edit-previous-conviction' => array('label' => 'Edit', 'class' => 'secondary', 'requireRows' => true),
                'delete-previous-conviction' =>
                    array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        ),
    ),
    'columns' => array(
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.conviction-date',
            'name' => 'convictionDate',
            'formatter' => 'Date',
            'type' => 'Action',
            'action' => 'edit-previous-conviction'
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.offence',
            'name' => 'categoryText',
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.name-of-court',
            'name' => 'courtFpn',
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.penalty',
            'name' => 'penalty',
        ),
        array(
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        ),
    )
);

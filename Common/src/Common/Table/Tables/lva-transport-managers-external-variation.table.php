<?php

return array(
    'variables' => array(
        'title' => '',
        'within_form' => true,
        'empty_message' => 'lva-transport-manager-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Add Transport Manager'),
            )
        ),
        'row-disabled-callback' => function ($row) {
            return isset($row['action']) && in_array($row['action'], ['D', 'C']);
        }
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Name',
            'formatter' => 'TransportManagerName',
            'internal' => false,
            'lva' => 'variation'
        ),
        array(
            'title' => 'Email',
            'name' => 'email'
        ),
        array(
            'title' => 'Date of birth',
            'name' => 'dob',
            'formatter' => 'TransportManagerDateOfBirth',
            'internal' => false,
            'lva' => 'variation',
        ),
        array(
            'type' => 'DeltaActionLinks'
        ),
    )
);

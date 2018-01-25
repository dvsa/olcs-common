<?php

return array(
    'variables' => array(
        'within_form' => true,
        'empty_message' => 'lva-transport-manager-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array()
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
            'internal' => true,
            'lva' => 'variation'
        ),
        array(
            'title' => 'Email',
            'name' => 'email'
        ),
        array(
            'title' => 'DOB',
            'name' => 'dob',
            'formatter' => 'TransportManagerDateOfBirth',
            'internal' => true,
            'lva' => 'variation',
        ),
        array(
            'type' => 'DeltaActionLinks',
        ),
    )
);

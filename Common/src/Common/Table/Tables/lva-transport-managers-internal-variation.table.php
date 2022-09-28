<?php

return array(
    'variables' => array(
        'title' => 'list-of-transport-managers',
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
            'title' => 'markup-table-th-remove-restore', //view partial from olcs-common
            'ariaDescription' => function ($row, $column) {
                $column['formatter'] = 'Name';
                return $this->callFormatter($column, $row['name']);
            },
            'type' => 'DeltaActionLinks',
        ),
    )
);

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
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Name',
            'formatter' => 'TransportManagerName',
            'internal' => false,
            'lva' => 'application',
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
            'lva' => 'application',
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => function ($row, $column) {
                $column['formatter'] = 'Name';
                return $this->callFormatter($column, $row['name']);
            },
            'type' => 'ActionLinks'
        ),
    )
);

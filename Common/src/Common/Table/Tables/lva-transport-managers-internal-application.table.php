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
                'delete' => array('class' => 'secondary', 'requireRows' => true)
            )
        ),
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Name',
            'formatter' => 'TransportManagerName',
            'internal' => true,
            'lva' => 'application',
        ),
        array(
            'title' => 'Email',
            'name' => 'email'
        ),
        array(
            'title' => 'DOB',
            'name' => 'dob',
            'formatter' => 'Date',
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

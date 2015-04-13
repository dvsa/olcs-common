<?php

return array(
    'variables' => array(
        'title' => '',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'delete' => array('class' => 'secondary', 'requireRows' => true),
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
            'formatter' => 'Date',
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'data-attributes' => array(
                'action'
            )
        )
    )
);

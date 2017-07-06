<?php

return array(
    'variables' => array(
        'within_form' => true,
        'empty_message' => 'lva-transport-manager-licence-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
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
            'lva' => 'licence',
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
            'type' => 'DeltaActionLinks'
        ),
    )
);

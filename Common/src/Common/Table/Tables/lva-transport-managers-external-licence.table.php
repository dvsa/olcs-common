<?php

return array(
    'variables' => array(
        'title' => '',
        'within_form' => true,
        'empty_message' => 'lva-transport-manager-licence-table-empty-message'
    ),
    'settings' => array(
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Name',
            'formatter' => 'TransportManagerName',
            'internal' => false,
            'lva' => 'licence',
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
            'lva' => 'licence',
        ),
        array(
            'type' => 'DeltaActionLinks'
        ),
    )
);

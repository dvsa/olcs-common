<?php

return array(
    'variables' => array(
        'title' => '',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(),
                'delete' => array(
                    'label' => 'action_links.remove',
                    'requireRows' => true
                )
            )
        )
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Name',
            'formatter' => 'TransportManagerName',
            'name' => 'name'
        ),
        array(
            'title' => 'Email',
            'name' => 'email'
        ),
        array(
            'title' => 'DOB',
            'name' => 'dob',
            'formatter' => 'TransportManagerDateOfBirth',
        ),
        array(
            'name' => 'select',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

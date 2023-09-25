<?php

use Common\Service\Table\Formatter\TransportManagerDateOfBirth;
use Common\Service\Table\Formatter\TransportManagerName;

return array(
    'variables' => array(
        'title' => 'list-of-transport-managers',
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
            'formatter' => TransportManagerName::class,
            'name' => 'name'
        ),
        array(
            'title' => 'Email',
            'name' => 'email'
        ),
        array(
            'title' => 'DOB',
            'name' => 'dob',
            'formatter' => TransportManagerDateOfBirth::class,
        ),
        array(
            'name' => 'select',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

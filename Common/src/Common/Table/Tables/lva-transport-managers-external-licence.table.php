<?php

use Common\Service\Table\Formatter\Name;
use Common\Service\Table\Formatter\TransportManagerDateOfBirth;
use Common\Service\Table\Formatter\TransportManagerName;

return [
    'variables' => [
        'title' => 'list-of-transport-managers',
        'within_form' => true,
        'empty_message' => 'lva-transport-manager-licence-table-empty-message'
    ],
    'settings' => [
    ],
    'attributes' => [
    ],
    'columns' => [
        [
            'title' => 'Name',
            'formatter' => TransportManagerName::class,
            'internal' => false,
            'lva' => 'licence',
        ],
        [
            'title' => 'Email',
            'name' => 'email'
        ],
        [
            'title' => 'Date of birth',
            'name' => 'dob',
            'formatter' => TransportManagerDateOfBirth::class,
            'internal' => false,
            'lva' => 'licence',
        ],
        [
            'title' => 'markup-table-th-remove-restore', //view partial from olcs-common
            'ariaDescription' => function ($row, $column) {
                $column['formatter'] = Name::class;
                return $this->callFormatter($column, $row['name']);
            },
            'type' => 'DeltaActionLinks'
        ],
    ]
];

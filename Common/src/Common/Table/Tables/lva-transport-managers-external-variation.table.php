<?php

use Common\Service\Table\Formatter\Name;
use Common\Service\Table\Formatter\TransportManagerDateOfBirth;
use Common\Service\Table\Formatter\TransportManagerName;

return [
    'variables' => [
        'title' => 'list-of-transport-managers',
        'within_form' => true,
        'empty_message' => 'lva-transport-manager-table-empty-message'
    ],
    'settings' => [
        'crud' => [
            'actions' => [
                'add' => ['label' => 'Add Transport Manager'],
            ]
        ],
        'row-disabled-callback' => fn($row) => isset($row['action']) && in_array($row['action'], ['D', 'C'])
    ],
    'attributes' => [
    ],
    'columns' => [
        [
            'title' => 'Name',
            'formatter' => TransportManagerName::class,
            'internal' => false,
            'lva' => 'variation'
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
            'lva' => 'variation',
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

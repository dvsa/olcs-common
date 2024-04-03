<?php

use Common\Service\Table\Formatter\Address;
use Common\Service\Table\Formatter\ConditionsUndertakingsType;
use Common\Service\Table\Formatter\Translate;
use Common\Service\Table\Formatter\YesNo;

return [
    'variables' => [
        'within_form' => true,
        'title' => 'lva-conditions-undertakings-table-title',
        'empty_message' => 'lva-conditions-undertakings-table-empty-message'
    ],
    'settings' => [
        'crud' => [
            'actions' => [
                'add' => [
                    'label' => 'action_links.add'
                ],
            ]
        ]
    ],
    'columns' => [
        [
            'title' => 'lva-conditions-undertakings-table-no',
            'type' => 'Action',
            'action' => 'edit',
            'formatter' => static function ($data, $column) {
                if (in_array($data['action'], ['U', 'D'])) {
                     return $data['licConditionVariation']['id'];
                }
                return $data['id'];
            }
        ],
        [
            'title' => 'lva-conditions-undertakings-table-type',
            'formatter' => ConditionsUndertakingsType::class,
        ],
        [
            'title' => 'lva-conditions-undertakings-table-added-via',
            'formatter' => Translate::class,
            'name' => 'addedVia->description'
        ],
        [
            'title' => 'lva-conditions-undertakings-table-fulfilled',
            'formatter' => YesNo::class,
            'name' => 'isFulfilled'
        ],
        [
            'title' => 'lva-conditions-undertakings-table-status',
            'formatter' => static fn($data) => $data['isDraft'] == 'Y' ? 'Draft' : 'Approved',
        ],
        [
            'title' => 'lva-conditions-undertakings-table-attached-to',
            'formatter' => function ($data, $column) {

                if (isset($data['operatingCentre']['address'])) {

                    $column['formatter'] = Address::class;

                    return $this->callFormatter($column, $data['operatingCentre']['address']);
                }

                return 'Licence';
            }
        ],
        [
            'title' => 'lva-conditions-undertakings-table-description',
            'name' => 'notes',
            'maxlength' => 30,
            'formatter' => \Common\Service\Table\Formatter\Comment::class
        ],
        [
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => function ($row, $column) {
                $column['formatter'] = ConditionsUndertakingsType::class;
                return $this->callFormatter($column, $row);
            },
            'type' => 'ActionLinks',
        ],
    ]
];

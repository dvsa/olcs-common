<?php

use Common\Service\Table\Formatter\Address;
use Common\Service\Table\Formatter\ConditionsUndertakingsType;
use Common\Service\Table\Formatter\Translate;
use Common\Service\Table\Formatter\YesNo;

return array(
    'variables' => array(
        'within_form' => true,
        'empty_message' => 'lva-conditions-undertakings-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'label' => 'action_links.add'
                ),
            )
        ),
        'row-disabled-callback' => function ($row) {
            return in_array($row['action'], ['D', 'C']);
        },
    ),
    'columns' => array(
        array(
            'title' => 'lva-conditions-undertakings-table-no',
            'type' => 'VariationRecordAction',
            'action' => 'edit',
            'formatter' => function ($data, $column) {
                if (in_array($data['action'], ['U', 'D'])) {
                    return $data['licConditionVariation']['id'];
                }

                return $data['id'];
            }
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-type',
            'formatter' => ConditionsUndertakingsType::class,
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-added-via',
            'formatter' => Translate::class,
            'name' => 'addedVia->description'
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-fulfilled',
            'formatter' => YesNo::class,
            'name' => 'isFulfilled'
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-status',
            'formatter' => function ($data) {
                return $data['isDraft'] == 'Y' ? 'Draft' : 'Approved';
            },
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-attached-to',
            'formatter' => function ($data, $column) {

                if (isset($data['operatingCentre']['address'])) {

                    $column['formatter'] = Address::class;

                    return $this->callFormatter($column, $data['operatingCentre']['address']);
                }

                return 'Licence';
            }
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-description',
            'name' => 'notes',
            'maxlength' => 30,
            'formatter' => \Common\Service\Table\Formatter\Comment::class
        ),
        array(
            'title' => 'markup-table-th-remove-restore', //view partial from olcs-common
            'type' => 'DeltaActionLinks',
        ),
    )
);

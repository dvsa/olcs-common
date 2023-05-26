<?php

return array(
    'variables' => array(
        'within_form' => true,
        'title' => 'lva-conditions-undertakings-table-title',
        'empty_message' => 'lva-conditions-undertakings-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'label' => 'action_links.add'
                ),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'lva-conditions-undertakings-table-no',
            'type' => 'Action',
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
            'formatter' => 'ConditionsUndertakingsType',
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-added-via',
            'formatter' => 'Translate',
            'name' => 'addedVia->description'
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-fulfilled',
            'formatter' => 'YesNo',
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

                    $column['formatter'] = 'Address';

                    return $this->callFormatter($column, $data['operatingCentre']['address']);
                }

                return 'Licence';
            }
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-description',
            'name' => 'notes',
            'maxlength' => 30,
            'formatter' => 'Comment'
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => function ($row, $column) {
                $column['formatter'] = 'ConditionsUndertakingsType';
                return $this->callFormatter($column, $row);
            },
            'type' => 'ActionLinks',
        ),
    )
);

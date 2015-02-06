<?php

return array(
    'variables' => array(
        'title' => 'lva-conditions-undertakings-table-title',
        'within_form' => true,
        'empty_message' => 'lva-conditions-undertakings-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'secondary', 'requireRows' => true),
                'restore' => array('class' => 'secondary', 'requireRows' => true)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'lva-conditions-undertakings-table-no',
            'type' => 'Action',
            'action' => 'edit',
            'name' => 'id'
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-type',
            'formatter' => 'Translate',
            'name' => 'conditionType->description'
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
            'formatter' => 'Translate',
            'name' => 'attachedTo->description'
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-s4',
            'format' => 'Todo'
        ),
        array(
            'title' => 'lva-conditions-undertakings-table-oc-address',
            'width' => '300px',
            'formatter' => function ($data, $column, $sm) {

                if (isset($data['operatingCentre']['address'])) {

                    $column['formatter'] = 'Address';

                    return $this->callFormatter($column, $data['operatingCentre']['address']);
                }

                return 'N/A';
            }
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

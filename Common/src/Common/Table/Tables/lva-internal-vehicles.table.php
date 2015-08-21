<?php

$translationPrefix = 'application_vehicle-safety_vehicle.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'empty_message' => 'application_vehicle-safety_vehicle.tableEmptyMessage',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true),
            )
        ),
        'paginate' => array(
            'limit' => array(
                'options' => array(10, 25, 50)
            )
        ),
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
        'collapseAt' => 3,
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'name' => 'vrm',
            'action' => 'edit',
            'formatter' => function ($data) {
                if (!is_null($data['interimApplication'])) {
                    return $data['vehicle']['vrm'] . ' (interim)';
                }

                return $data['vehicle']['vrm'];
            },
            'type' => 'Action',
        ),
        array(
            'title' => $translationPrefix . '.weight',
            'stringFormat' => '{vehicle->platedWeight} kg',
            'formatter' => 'StackValueReplacer'
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'formatter' => 'Date',
            'name' => 'specifiedDate'
        ),
        array(
            'title' => $translationPrefix . '.removed',
            'formatter' => 'Date',
            'name' => 'removalDate'
        ),
        array(
            'title' => $translationPrefix . '.disc-no',
            'name' => 'discNo',
            'formatter' => 'VehicleDiscNo'
        ),
        array(
            'name' => 'action',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

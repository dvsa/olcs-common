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
                'add' => array(
                    'label' => 'vehicle_table_action.add.label',
                    'class' => 'primary'
                ),
                'edit' => array(
                    'label' => 'vehicle_table_action.edit.label',
                    'requireRows' => true
                ),
                'delete' => array(
                    'label' => 'vehicle_table_action.delete.label',
                    'class' => 'secondary',
                    'requireRows' => true
                ),
                // @note other actions may be added dynamically,
                // @see Common\Controller\Lva\AbstractGoodsVehiclesController
            )
        ),
        'paginate' => array(
            'limit' => array(
                'options' => array(10, 25, 50)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'stack' => 'vehicle->vrm',
            'formatter' => 'StackValue',
            'action' => 'edit',
            'type' => 'Action',
        ),
        array(
            'title' => $translationPrefix . '.weight',
            'stringFormat' => '{vehicle->platedWeight} Kg',
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

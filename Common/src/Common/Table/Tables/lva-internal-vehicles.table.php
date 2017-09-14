<?php

use Common\Controller\Lva\AbstractGoodsVehiclesController;

$translationPrefix = 'application_vehicle-safety_vehicle.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'titleSingular' => $translationPrefix . '.titleSingular',
        'empty_message' => 'application_vehicle-safety_vehicle.tableEmptyMessage',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(),
                'delete' => array(
                    'label' => 'action_links.remove',
                    'class' => ' more-actions__item js-require--multiple',
                    'requireRows' => true
                )
            )
        ),
        'paginate' => [
            'limit' => [
                'default' => AbstractGoodsVehiclesController::DEF_TABLE_ITEMS_COUNT,
                'options' => [10, 25, 50],
            ],
        ],
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
        'collapseAt' => 1,
        'row-disabled-callback' => function ($row) {
            return $row['removalDate'] !== null;
        }
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'name' => 'vrm',
            'action' => 'edit',
            'formatter' => 'VehicleRegistrationMark',
            'type' => 'Action',
            'sort' => 'v.vrm',
        ),
        array(
            'title' => $translationPrefix . '.weight',
            'stringFormat' => '{vehicle->platedWeight} kg',
            'formatter' => 'StackValueReplacer'
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'formatter' => 'Date',
            'name' => 'specifiedDate',
            'sort' => 'specifiedDate'
        ),
        array(
            'title' => $translationPrefix . '.removed',
            'formatter' => 'Date',
            'name' => 'removalDate',
            'sort' => 'removalDate'
        ),
        array(
            'title' => $translationPrefix . '.disc-no',
            'name' => 'discNo',
            'formatter' => 'VehicleDiscNo'
        ),
        array(
            'type' => 'ActionLinks',
            'isRemoveVisible' => function ($data) {
                return empty($data['removalDate']);
            }
        ),
        array(
            'name' => 'action',
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'disableIfRowIsDisabled' => true
        )
    )
);

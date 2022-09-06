<?php

$translationPrefix = 'application_vehicle-safety_vehicle-psv.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'titleSingular' => $translationPrefix . '.title.singular',
        'empty_message' => $translationPrefix . '.empty_message',
        'required_label' => 'vehicle',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'id' => 'addSmall'
                ),
                'delete' => array(
                    'label' => 'action_links.remove',
                    'class' => ' more-actions__item action--secondary',
                    'requireRows' => true
                ),
                'transfer' => array(
                    'label' => 'Transfer',
                    'class' => ' more-actions__item js-require--multiple action--secondary',
                    'requireRows' => true,
                    'id' => 'transferSmall'
                )
            )
        ),
        'row-disabled-callback' => function ($row) {
            return $row['removalDate'] !== null;
        },
        'paginate' => array(
            'limit' => array(
                'options' => array(10, 25, 50)
            )
        ),
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
        'collapseAt' => 1
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'stack' => 'vehicle->vrm',
            'formatter' => 'StackValue',
            'action' => 'edit',
            'type' => 'Action',
            'sort' => 'v.vrm',
        ),
        array(
            'title' => $translationPrefix . '.make',
            'stack' => 'vehicle->makeModel',
            'formatter' => 'StackValue'
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'name' => 'specifiedDate',
            'formatter' => 'Date',
            'sort' => 'specifiedDate'
        ),
        array(
            'title' => $translationPrefix . '.removed',
            'name' => 'removalDate',
            'formatter' => 'Date',
            'sort' => 'removalDate'
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'type' => 'ActionLinks',
            'ariaDescription' => function ($row) {
                return $row['vehicle']['vrm'];
            },
            'isRemoveVisible' => function ($data) {
                return empty($data['removalDate']);
            },
            'deleteInputName' => 'vehicles[action][delete][%d]'
        ),
        array(
            'markup-table-th-action', //this is a view partial from olcs-common
            'name' => 'action',
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'disableIfRowIsDisabled' => true
        )
    )
);

<?php

$translationPrefix = 'application_vehicle-safety_vehicle-psv.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'titleSingular' => $translationPrefix . '.titleSingular',
        'empty_message' => $translationPrefix . '.empty_message',
        'required_label' => 'vehicle',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary', 'id' => 'addSmall'),
                'edit' => array('requireRows' => true, 'id' => 'editSmall'),
                'delete' => array('class' => 'secondary', 'requireRows' => true, 'id' => 'deleteSmall'),
                'transfer' => array(
                    'label' => 'Transfer',
                    'class' => 'secondary js-require--multiple',
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
        ),
        array(
            'title' => $translationPrefix . '.make',
            'stack' => 'vehicle->makeModel',
            'formatter' => 'StackValue'
        ),
        array(
            'name' => 'action',
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'disableIfRowIsDisabled' => true
        )
    )
);

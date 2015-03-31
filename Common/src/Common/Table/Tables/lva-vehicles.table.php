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
                'print-vehicles' => array('label' => 'Print vehicle list', 'requireRows' => true),
                'reprint' => array('label' => 'Reprint Disc', 'requireRows' => true),
                'edit' => array('requireRows' => true),
                'delete' => array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        ),
        /* Temp commented out until we alter the filtering mechanism
        'paginate' => array(
            'limit' => array(
                'options' => array(10, 25, 50)
            )
        )*/
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'name' => 'vrm',
            'action' => 'edit',
            'type' => 'Action',
        ),
        array(
            'title' => $translationPrefix . '.weight',
            'format' => '{{platedWeight}} Kg'
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
            'name' => 'discNo'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

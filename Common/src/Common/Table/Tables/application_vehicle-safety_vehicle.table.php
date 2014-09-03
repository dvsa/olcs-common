<?php

$translationKeyPrefix = 'application_vehicle-safety_vehicle.table.';

return array(
    'variables' => array(
        'title' => $translationKeyPrefix . 'title'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'warning', 'requireRows' => true)
            )
        ),
        'paginate' => array(
            'limit' => array(
                'default' => 10,
                'options' => array(10, 25, 50)
            )
        )
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'width' => 'checkbox',
            'format' => '{{[elements/radio]}}'
        ),
        array(
            'title' => $translationKeyPrefix . 'vrm',
            'formatter' => $this->getServiceLocator()->get('section.vehicle-safety.vehicle.formatter.vrm')
        ),
        array(
            'title' => $translationKeyPrefix . 'weight',
            'format' => '{{platedWeight}} Kg'
        ),
        array(
            'title' => $translationKeyPrefix . 'specified',
            'formatter' => 'Date',
            'name' => 'specifiedDate'
        ),
        array(
            'title' => $translationKeyPrefix . 'removed',
            'formatter' => 'Date',
            'name' => 'deletedDate'
        ),
        array(
            'title' => $translationKeyPrefix . 'disc-no',
            'name' => 'discNo'
        )
    )
);

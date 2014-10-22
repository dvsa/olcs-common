<?php

$translationPrefix = 'application_vehicle-safety_vehicle.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'reprint' => array('label' => 'Reprint Disc', 'requireRows' => true),
                'edit' => array('requireRows' => true),
                'delete' => array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        ),
        'paginate' => array(
            'limit' => array(
                'default' => 10,
                'options' => array(10, 25, 50)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'formatter' => $this->getServiceLocator()->get('section.vehicle-safety.vehicle.formatter.vrm')
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

<?php

use Common\Service\Table\Formatter\Date;
use Common\Service\Table\Formatter\StackValue;

$translationPrefix = 'application_vehicle-safety_vehicle-psv.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'empty_message' => $translationPrefix . '.empty_message',
        'required_label' => 'vehicle',
        'within_form' => true
    ),
    'settings' => array(
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
            'formatter' => StackValue::class,
            'sort' => 'v.vrm'
        ),
        array(
            'title' => $translationPrefix . '.make',
            'stack' => 'vehicle->makeModel',
            'formatter' => StackValue::class,
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'formatter' => Date::class,
            'name' => 'specifiedDate',
            'sort' => 'specifiedDate'
        ),
        array(
            'title' => $translationPrefix . '.removed',
            'formatter' => Date::class,
            'name' => 'removalDate',
            'sort' => 'removalDate'
        )
    )
);

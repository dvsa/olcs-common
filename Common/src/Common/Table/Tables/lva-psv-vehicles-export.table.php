<?php

use Common\Service\Table\Formatter\Date;

$translationPrefix = 'application_vehicle-safety_vehicle-psv.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'titleSingular' => $translationPrefix . '.title.singular',
        'empty_message' => $translationPrefix . '.empty_message',
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.vrm',
            'name' => 'vrm',
        ),
        array(
            'title' => $translationPrefix . '.make',
            'name' => 'makeModel',
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'name' => 'specifiedDate',
            'formatter' => Date::class,
        ),
        array(
            'title' => $translationPrefix . '.removed',
            'name' => 'removalDate',
            'formatter' => Date::class,
        )
    )
);

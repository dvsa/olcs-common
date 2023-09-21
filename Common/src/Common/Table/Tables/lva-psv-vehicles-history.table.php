<?php

$translationPrefix = 'application_vehicle-safety_vehicle-history.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title'
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.licence',
            'name' => 'licenceNo',
            'formatter' => function ($data) {
                return isset($data['licence']) ? $data['licence']['licNo'] : '';
            }
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'name' => 'specifiedDate',
            'formatter' => \Common\Service\Table\Formatter\DateTime::class,
        ),
        array(
            'title' => $translationPrefix . '.removed',
            'name' => 'removalDate',
            'formatter' => \Common\Service\Table\Formatter\DateTime::class,
        )
    )
);

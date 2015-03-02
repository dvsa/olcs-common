<?php

$translationPrefix = 'licence_goods-trailers_trailer.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'empty_message' => $translationPrefix . '.tableEmptyMessage',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.trailerNo',
            'name' => 'trailerNo',
            'action' => 'edit',
            'type' => 'Action',
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'formatter' => 'Date',
            'name' => 'specifiedDate'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

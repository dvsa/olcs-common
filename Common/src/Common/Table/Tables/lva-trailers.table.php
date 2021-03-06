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
                'add' => array(),
                'delete' => array(
                    'label' => 'action_links.remove',
                    'requireRows' => true
                )
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.trailerNo',
            'name' => 'trailerNo',
            'action' => 'edit',
            'type' => 'Action',
            'keepForReadOnly' => true,
        ),
        array(
            'title' => $translationPrefix . '.specified',
            'formatter' => 'Date',
            'name' => 'specifiedDate'
        ),
        array(
            'type' => 'ActionLinks',
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

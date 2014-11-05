<?php

$translationPrefix = 'lva-financial-evidence-table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'empty_message' => $translationPrefix . '.empty_message',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'upload' => array('class' => 'primary'),
                'delete' => array('class' => 'secondary', 'requireRows' => true)
            )
        )
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.file-name',
            'name' => 'fileName'
        ),
        array(
            'title' => $translationPrefix . '.type',
            'name' => 'type'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

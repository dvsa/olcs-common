<?php

$translationPrefix = 'psv_discs.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Request new discs', 'class' => 'primary'),
                'replace' => array('label' => 'Replace', 'requireRows' => true),
                'void' => array('label' => 'Void', 'requireRows' => true),
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
            'title' => $translationPrefix . '.discNo',
            'name' => 'discNo'
        ),
        array(
            'title' => $translationPrefix . '.issuedDate',
            'name' => 'issuedDate'
        ),
        array(
            'title' => $translationPrefix . '.ceasedDate',
            'name' => 'ceasedDate'
        ),
        array(
            'title' => $translationPrefix . '.replacement',
            'name' => 'isCopy',
            'formatter' => 'YesNo'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

<?php

return array(
    'settings' => array(
        'title' => 'Table',
        'view' => 'crud',
        'action' => '/',
        'actions' => array(
            'add' => array('class' => 'primary'),
            'edit' => array('class' => 'secondary'),
            'delete' => array('class' => 'warning')
        )
    ),
    'attributes' => array(
        'class' => 'table'
    ),
    'columns' => array(
        array(
            'title' => 'Select',
            'format' => '{{[elements/radio]}}'
        ),
        array(
            'title' => 'Name',
            'format' => '{{name}}'
        )
    )
);

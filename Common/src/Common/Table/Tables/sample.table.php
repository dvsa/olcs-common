<?php

return array(
    'settings' => array(
        'title' => 'Table',
        'view' => 'crud',
        'paginate' => true,
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
            'title' => '',
            'width' => 'checkbox',
            'format' => '{{[elements/radio]}}'
        ),
        array(
            'title' => 'Name',
            'name' => 'name',
            'sort' => 'operatorName'
        )
    )
);

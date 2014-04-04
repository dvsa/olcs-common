<?php

return array(
    'variables' => array(
        'title' => 'Case list'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array(),
                'delete' => array('class' => 'warning')
            )
        )
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Select',
            'format' => '{{[elements/radio]}}'
        ),
        array(
            'title' => 'Case Number',
            'formatter' => function($row) {
                return '<a href="' . $this->url->fromRoute('case_manage', array('case' => $row['id'], 'action' => 'overview')) . '">' . $row['caseNumber'] . '</a>';
            }
        ),
        array(
            'title' => 'Status',
            'name' => 'status'
        ),
        array(
            'title' => 'Description',
            'name' => 'description'
        ),
        array(
            'title' => 'ECMS',
            'name' => 'ecms'
        )
    )
);

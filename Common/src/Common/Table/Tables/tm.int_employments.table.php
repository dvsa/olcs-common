<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.employments.table',
        //'within_form' => true,
        'empty_message' => 'transport-manager.employments.table.empty',
    ),
    'data-group' => 'otherEmployment',
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Add', 'class' => 'primary'),
                'edit' => array('label' => 'Edit', 'class' => 'secondary', 'requireRows' => true),
                'delete' => array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        ),
    ),
    'columns' => array(
        array(
            'title' => 'Employer',
            'name' => 'employerName',
            'type' => 'Action',
            'action' => 'edit',
        ),
        array(
            'title' => 'Address',
            'name' => 'contactDetails->address',
            'formatter' => 'Address'
        ),
        array(
            'title' => 'Position',
            'name' => 'position',
        ),
        array(
            'title' => 'Hours / days',
            'name' => 'hoursPerWeek',
        ),
        array(
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        ),
    )
);

<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.employments.table',
        'empty_message' => 'transport-manager.employments.table.empty',
    ),
    'data-group' => 'otherEmployment',
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'transport-manager.employments.table.add', 'class' => 'primary'),
            )
        ),
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
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
            'type' => 'ActionLinks',
            'deleteInputName' => 'action[delete][%d]'
        ),
    )
);

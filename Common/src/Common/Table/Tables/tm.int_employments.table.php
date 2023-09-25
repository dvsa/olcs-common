<?php

use Common\Service\Table\Formatter\Address;

return array(
    'variables' => array(
        'title' => 'transport-manager.employments.table',
        'empty_message' => 'transport-manager.employments.table.empty',
    ),
    'data-group' => 'otherEmployment',
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'label' => 'transport-manager.employments.table.add'
                )
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
            'formatter' => Address::class
        ),
        array(
            'title' => 'Position',
            'name' => 'position',
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => 'employerName',
            'type' => 'ActionLinks',
            'deleteInputName' => 'action[delete][%d]'
        ),
    )
);

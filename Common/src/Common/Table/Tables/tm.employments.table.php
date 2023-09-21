<?php

use Common\Service\Table\Formatter\Address;

return array(
    'variables' => array(
        'title' => 'transport-manager.employments.table',
        'within_form' => true,
        'empty_message' => false,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-employment' => array(
                    'label' => 'transport-manager.employments.table.add',
                )
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'Employer',
            'name' => 'employerName',
            'type' => 'Action',
            'action' => 'edit-employment',
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
            'deleteInputName' => 'employment[action][delete-employment][%d]'
        )
    )
);

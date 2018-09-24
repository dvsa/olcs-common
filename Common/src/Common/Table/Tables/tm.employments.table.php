<?php

return array(
    'variables' => array(
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
            'formatter' => 'Address'
        ),
        array(
            'title' => 'Position',
            'name' => 'position',
        ),
        array(
            'type' => 'ActionLinks',
            'deleteInputName' => 'employment[action][delete-employment][%d]'
        )
    )
);

<?php

return array(
    'variables' => array(
        'title' => 'selfserve-app-subSection-your-business-people-tableHeaderPartners',
        'empty_message' => 'selfserve-app-subSection-your-business-people-tableEmptyMessage',
        'required_label' => 'person',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array(
                    'label' => 'people_table_action.delete.label',
                    'class' => 'secondary',
                    'requireRows' => true,
                ),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnName',
            'type' => 'Action',
            'action' => 'edit',
            'formatter' => 'Name'
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnHasOtherNames',
            'name' => 'otherName',
            'formatter' => function ($row) {
                return ($row['otherName'] ? 'Yes' : 'No');
            }
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnDate',
            'name' => 'birthDate',
            'formatter' => 'Date',
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnPosition',
            'name' => 'position',
        ),
        array(
            'name' => 'select',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

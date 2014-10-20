<?php

return array(
    'variables' => array(
        'title' => 'application_your-business_business_details-subsidiaries-tableHeader',
        'empty_message' => 'application_your-business_business_details-subsidiaries-tableEmptyMessage',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'secondary', 'requireRows' => true)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnName',
            'name' => 'name',
            'class' => 'action--tertiary',
            'action' => 'edit',
            'type' => 'Action'
        ),
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnCompanyNo',
            'name' => 'companyNo',
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

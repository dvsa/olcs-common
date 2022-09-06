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
                'add' => array('label' => 'Add subsidiary'),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnName',
            'name' => 'name',
            'action' => 'edit',
            'type' => 'Action',
            'keepForReadOnly' => true,
        ),
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnCompanyNo',
            'name' => 'companyNo'
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => 'name',
            'type' => 'ActionLinks',
        ),
    )
);

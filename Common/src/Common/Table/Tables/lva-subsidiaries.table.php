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
                'add' => array('class' => 'tertiary large','label' => 'Add subsidiary'),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnName',
            'name' => 'name',
            'action' => 'edit',
            'type' => 'Action'
        ),
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnCompanyNo',
            'name' => 'companyNo'
        ),
        array(
            'type' => 'ActionLinks',
        ),
    )
);

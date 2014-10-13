<?php

return array(
    'variables' => array(
        'title' => 'application_your-business_business_details-subsidiaries-tableHeader',
        'within_form' => true,
        'empty_message' => 'application_your-business_business_details-subsidiaries-tableEmptyMessage'
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
    'attributes' => array(),
    'columns' => array(
        array(
            'width' => 'checkbox',
            'type' => 'Selector'
        ),
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnName',
            'name' => 'name',
            'formatter' => function ($row) {
                return '<a href="' . $this->generateUrl(
                    array(
                        'child_id' => $row['id'],
                        'action' => 'edit'
                    ),
                    'lva-application/business_details'
                ) . '">' . $row['name'] . '</a>';
            }
        ),
        array(
            'title' => 'application_your-business_business_details-subsidiaries-columnCompanyNo',
            'name' => 'companyNo',
        ),
    ),
    // Footer configuration
    'footer' => array(
    )
);

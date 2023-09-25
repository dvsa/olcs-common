<?php

use Common\Service\Table\Formatter\Address;

$translationPrefix = 'application_taxi-phv_licence.table';

return array(
    'variables' => array(
        'title' => '',
        'empty_message' => $translationPrefix . '.empty_message',
        'required_label' => 'licence',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Add Taxi or PHV licence'),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.licence-number',
            'action' => 'edit',
            'name' => 'privateHireLicenceNo',
            'type' => 'Action'
        ),
        array(
            'title' => $translationPrefix . '.council',
            'name' => 'councilName'
        ),
        array(
            'title' => $translationPrefix . '.address',
            'formatter' => Address::class,
            'name' => 'address'
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => 'privateHireLicenceNo',
            'type' => 'ActionLinks',
        ),
    )
);

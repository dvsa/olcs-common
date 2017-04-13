<?php
$prefix = 'selfserve-app-subSection-previous-history-previous-licence-';

return array(
    'variables' => array(
        'title' => false,
        'within_form' => true,
        'empty_message' => false,
    ),
    'settings' => array(
        'crud' => array(
            'formName' => 'current',
            'actions' => array(
                'add' => array('label' => 'Add licence details'),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $prefix . 'columnLicNo',
            'name' => 'licNo',
            'type' => 'Action',
            'action' => 'edit'
        ),
        array(
            'title' => $prefix . 'columnHolderName',
            'name' => 'holderName',
        ),
        array(
            'title' => $prefix . 'columnWillSurrender',
            'name' => 'willSurrender',
            'formatter' => 'YesNo'
        ),
        array(
            'type' => 'ActionLinks',
            'deleteInputName' => 'data[prevHasLicence-table][action][delete][%d]'
        )
    )
);

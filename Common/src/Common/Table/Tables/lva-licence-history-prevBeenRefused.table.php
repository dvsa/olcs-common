<?php
$prefix = 'selfserve-app-subSection-previous-history-previous-licence-';

return array(
    'variables' => array(
        'within_form' => true,
        'empty_message' => false
    ),
    'settings' => array(
        'crud' => array(
            'formName' => 'refused',
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
            'type' => 'ActionLinks',
            'deleteInputName' => 'eu[prevBeenRefused-table][action][delete][%d]'
        )
    )
);

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
            'formName' => 'disqualified',
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
            'title' => $prefix . 'columnDisqualificationDate',
            'name' => 'disqualificationDate',
            'formatter' => 'Date'
        ),
        array(
            'title' => $prefix . 'columnDisqualificationLength',
            'name' => 'disqualificationLength',
        ),
        array(
            'type' => 'ActionLinks',
            'deleteInputName' => 'data[prevBeenDisqualifiedTc-table][action][delete][%d]'
        )
    )
);

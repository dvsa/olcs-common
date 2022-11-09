<?php

return array(
    'variables' => array(
        'empty_message' => false,
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-previous-licence' => array(
                    'label' => 'transport-manager.previouslicences.table.add'
                )
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'transport-manager.previouslicences.table.lic-no',
            'name' => 'licNo',
            'type' => 'Action',
            'action' => 'edit-previous-licence'
        ),
        array(
            'title' => 'transport-manager.previouslicences.table.holderName',
            'name' => 'holderName',
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => 'licNo',
            'type' => 'ActionLinks',
            'deleteInputName' => 'previousLicences[action][delete-previous-licence][%d]'
        )
    )
);

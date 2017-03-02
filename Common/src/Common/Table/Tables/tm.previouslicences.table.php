<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.previouslicences.table',
        'empty_message' => 'transport-manager.previouslicences.table.empty',
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
            'type' => 'ActionLinks',
            'deleteInputName' => 'previousLicences[action][delete-previous-licence][%d]'
        )
    )
);

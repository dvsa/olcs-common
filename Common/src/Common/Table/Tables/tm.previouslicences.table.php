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
                'add-previous-licence' => array('label' => 'Add', 'class' => 'primary'),
                'edit-previous-licence' => array('label' => 'Edit', 'class' => 'secondary', 'requireRows' => true),
                'delete-previous-licence' => array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        ),
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
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        ),
    )
);

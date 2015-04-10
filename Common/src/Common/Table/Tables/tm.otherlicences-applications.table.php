<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.otherlicences.table',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-other-licence-applications' => array('label' => 'Add', 'class' => 'primary'),
                'edit-other-licence-applications' => array(
                    'label' => 'Edit',
                    'class' => 'secondary js-require--one',
                    'requireRows' => true
                ),
                'delete-other-licence-applications' => array(
                    'label' => 'Remove',
                    'class' => 'secondary js-require--multiple',
                    'requireRows' => true
                )
            ),
        ),
    ),
    'columns' => array(
        array(
            'title' => 'transport-manager.otherlicences.table.lic_no',
            'name' => 'licNo',
            'type' => 'Action',
            'action' => 'edit-other-licence-applications'
        ),
        array(
            'title' => 'transport-manager.otherlicences.table.role',
            'name' => 'role',
            'formatter' => 'RefData'
        ),
        array(
            'title' => 'transport-manager.otherlicences.table.operating_centres',
            'name' => 'operatingCentres',
        ),
        array(
            'title' => 'transport-manager.otherlicences.table.total_auth_vehicles',
            'name' => 'totalAuthVehicles',
        ),
        array(
            'title' => 'transport-manager.otherlicences.table.hours_per_week',
            'name' => 'hoursPerWeek',
        ),
        array(
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        ),
    )
);

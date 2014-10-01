<?php

return array(
    'variables' => array(
        'title' => 'application_operating-centres_authorisation.table.title',
        'within_form' => true,
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
            'title' => 'application_operating-centres_authorisation.table.address',
            'formatter' => function ($data, $column) {

                $column['formatter'] = 'Address';

                return '<a href="' . $this->generateUrl(
                    array('action' => 'edit', 'id' => $data['id']),
                    null,
                    true
                ) . '">' . $this->callFormatter($column, $data) . '</a>';
            },
            'name' => 'address'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.vehicles',
            'name' => 'noOfVehiclesPossessed'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.trailers',
            'name' => 'noOfTrailersPossessed'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.permission',
            'name' => 'permission',
            'formatter' => 'YesNo'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.advertised',
            'name' => 'adPlaced',
            'formatter' => 'YesNo'
        )
    ),
    'footer' => array(
        'total' => array(
            'type' => 'th',
            'content' => 'application_operating-centres_authorisation.table.footer.total',
            'formatter' => 'Translate',
            'colspan' => 2
        ),
        array(
            'formatter' => 'Sum',
            'name' => 'noOfVehiclesPossessed'
        ),
        'trailersCol' => array(
            'formatter' => 'Sum',
            'name' => 'noOfTrailersPossessed'
        ),
        'remainingColspan' => array(
            'colspan' => 2
        )
    )
);

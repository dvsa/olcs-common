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
            'title' => 'application_operating-centres_authorisation.table.address',
            'formatter' => function ($data, $column) {

                $column['formatter'] = 'Address';

                return '<a href="' . $this->generateUrl(
                    array('action' => 'edit', 'child_id' => $data['id']),
                    'lva-' . $column['type'] . '/operating_centres'
                ) . '">' . $this->callFormatter($column, $data) . '</a>';
            },
            'name' => 'address'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.vehicles',
            'name' => 'noOfVehiclesRequired'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.trailers',
            'name' => 'noOfTrailersRequired'
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
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    ),
    'footer' => array(
        'total' => array(
            'type' => 'th',
            'content' => 'application_operating-centres_authorisation.table.footer.total',
            'formatter' => 'Translate',
            'colspan' => 1
        ),
        array(
            'formatter' => 'Sum',
            'name' => 'noOfVehiclesRequired'
        ),
        'trailersCol' => array(
            'formatter' => 'Sum',
            'name' => 'noOfTrailersRequired'
        ),
        'remainingColspan' => array(
            'colspan' => 3
        )
    )
);

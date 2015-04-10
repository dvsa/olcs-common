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
                'delete' => array('class' => 'secondary', 'requireRows' => true),
                'restore' => array('class' => 'secondary', 'requireRows' => true)
            )
        ),
        'row-disabled-callback' => function ($row) {
            return in_array($row['action'], ['D', 'C']);
        }
    ),
    'attributes' => array(),
    'columns' => array(
        array(
            'title' => 'application_operating-centres_authorisation.table.address',
            'type' => 'VariationRecordAction',
            'action' => 'edit',
            'formatter' => 'Address',
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
            'width' => 'checkbox',
            'type' => 'Selector',
            'data-attributes' => array(
                'action'
            )
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
            'formatter' => 'OpCentreDeltaSum',
            'name' => 'noOfVehiclesRequired'
        ),
        'trailersCol' => array(
            'formatter' => 'OpCentreDeltaSum',
            'name' => 'noOfTrailersRequired'
        ),
        'remainingColspan' => array(
            'colspan' => 3
        )
    )
);

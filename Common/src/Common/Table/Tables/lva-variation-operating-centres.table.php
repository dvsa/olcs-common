<?php

return array(
    'variables' => array(
        'title' => 'application_operating-centres_authorisation.table.title',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'label' => 'Add operating centre'
                ),
                'schedule41' => array(
                    'value' => 'Add schedule 4/1',
                    'requireRows' => false
                )
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
            'type' => 'OperatingCentreVariationRecordAction',
            'action' => 'edit',
            'name' => 'operatingCentre->address',
            'formatter' => 'Address',
            'addressFields' => 'BRIEF',
            'sort' => 'adr',
            'width' => '60%'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.vehicles-hgv',
            'name' => 'noOfHgvVehiclesRequired',
            'sort' => 'noOfHgvVehiclesRequired'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.vehicles-lgv',
            'name' => 'noOfLgvVehiclesRequired',
            'formatter' => 'OcNoOfLgvVehiclesRequired',
            'sort' => 'noOfLgvVehiclesRequired'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.trailers',
            'name' => 'noOfTrailersRequired',
            'sort' => 'noOfTrailersRequired'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.complaints',
            'name' => 'noOfComplaints',
            'formatter' => 'OcComplaints'
        ),
        array(
            'type' => 'DeltaActionLinks'
        ),
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
            'name' => 'noOfHgvVehiclesRequired'
        ),
        'noOfLgvVehiclesRequired' => array(
            'formatter' => 'OpCentreDeltaSum',
            'name' => 'noOfLgvVehiclesRequired'
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

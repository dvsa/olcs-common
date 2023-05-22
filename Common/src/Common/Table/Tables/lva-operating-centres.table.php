<?php

return array(
    'variables' => array(
        'title' => 'application_operating-centres_authorisation.table.title',
        'empty_message' => 'application_operating-centres_authorisation-tableEmptyMessage',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Add operating centre'),
                'schedule41' => array(
                    'value' => 'Add schedule 4/1',
                    'requireRows' => false
                )
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'application_operating-centres_authorisation.table.address',
            'type' => 'OperatingCentreAction',
            'action' => 'edit',
            'name' => 'operatingCentre->address',
            'formatter' => 'Address',
            'addressFields' => 'BRIEF',
            'sort' => 'adr',
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.vehicles',
            'isNumeric' => true,
            'name' => 'noOfVehiclesRequired',
            'sort' => 'noOfVehiclesRequired'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.trailers',
            'isNumeric' => true,
            'name' => 'noOfTrailersRequired',
            'sort' => 'noOfTrailersRequired'
        ),
        array(
            'title' => 'application_operating-centres_authorisation.table.complaints',
            'isNumeric' => true,
            'name' => 'noOfComplaints',
            'formatter' => 'OcComplaints'
        ),
        array(
            'title' => 'markup-table-th-remove',
            'ariaDescription' => function($row) {
                return $row['operatingCentre']['address']['addressLine1'];
            },
            'type' => 'ActionLinks'
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
            'formatter' => 'Sum',
            'align' => 'govuk-!-text-align-right',
            'name' => 'noOfVehiclesRequired'
        ),
        'trailersCol' => array(
            'formatter' => 'Sum',
            'align' => 'govuk-!-text-align-right',
            'name' => 'noOfTrailersRequired'
        ),
        'remainingColspan' => array(
            'colspan' => 3
        )
    )
);

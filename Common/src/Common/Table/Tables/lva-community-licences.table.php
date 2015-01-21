<?php

return array(
    'variables' => array(
        'title' => 'lva-community-licences-table-title',
        'within_form' => true,
        'empty_message' => 'lva-community-licences-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(

            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'lva-community-licences-table-column-prefix',
            'name' => 'serialNoPrefix',
        ),
        array(
            'title' => 'lva-community-licences-table-column-status',
            'name' => 'status',
            'formatter' => 'RefData'
        ),
        array(
            'title' => 'lva-community-licences-table-column-issue-date',
            'name' => 'specifiedDate',
            'formatter' => 'Date'
        ),
        array(
            'title' => 'lva-community-licences-table-column-issue-number',
            'name' => 'issueNo',
            'formatter' => 'CommunityLicenceIssueNo'
        ),
        array(
            'title' => 'lva-community-licences-table-column-ceased-date',
            'name' => 'expiredDate',
            'formatter' => 'Date'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Selector'
        )
    )
);

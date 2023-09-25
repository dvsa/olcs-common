<?php

use Common\Service\Table\Formatter\CommunityLicenceIssueNo;
use Common\Service\Table\Formatter\CommunityLicenceStatus;
use Common\Service\Table\Formatter\Date;

return array(
    'variables' => array(
        'title' => 'lva-community-licences-table-title',
        'within_form' => true,
        'empty_message' => 'lva-community-licences-table-empty-message'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'label' => 'Add'
                ),
                'office-licence-add' => array(
                    'label' => 'Add office licence'
                ),
                'annul' => array(
                    'label' => 'Annul',
                    'requireRows' => true
                ),
                'restore' => array(
                    'label' => 'Restore',
                    'requireRows' => true
                ),
                'stop' => array(
                    'label' => 'Stop',
                    'requireRows' => true
                ),
                'reprint' => array(
                    'label' => 'Reprint',
                    'requireRows' => true
                )
            )
        ),
        'row-disabled-callback' => function ($row) {
            return in_array(
                $row['status']['id'],
                [
                    Common\RefData::COMMUNITY_LICENCE_STATUS_EXPIRED,
                    Common\RefData::COMMUNITY_LICENCE_STATUS_VOID,
                    Common\RefData::COMMUNITY_LICENCE_STATUS_RETURNDED
                ]
            );
        },
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
    ),
    'columns' => array(
        array(
            'title' => 'lva-community-licences-table-column-prefix',
            'name' => 'serialNoPrefix',
        ),
        array(
            'title' => 'lva-community-licences-table-column-issue-date',
            'name' => 'specifiedDate',
            'formatter' => Date::class
        ),
        array(
            'title' => 'lva-community-licences-table-column-issue-number',
            'name' => 'issueNo',
            'formatter' => CommunityLicenceIssueNo::class
        ),
        array(
            'title' => 'lva-community-licences-table-column-status',
            'name' => 'status',
            'formatter' => CommunityLicenceStatus::class
        ),
        array(
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'disableIfRowIsDisabled' => true,
            'data-attributes' => ['status']
        ),
    )
);

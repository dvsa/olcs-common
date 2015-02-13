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
                'add' => array('label' => 'Add', 'class' => 'primary'),
                'office-licence-add' => array(
                    'label' => 'Add office licence',
                    'class' => 'secondary',
                    'requireRows' => true
                ),
                'void' => array('label' => 'Void', 'class' => 'secondary', 'requireRows' => true),
            )
        ),
        'row-disabled-callback' => function ($row) {
            return in_array(
                $row['status']['id'],
                [
                    Common\Service\Entity\CommunityLicEntityService::STATUS_EXPIRED,
                    Common\Service\Entity\CommunityLicEntityService::STATUS_VOID,
                    Common\Service\Entity\CommunityLicEntityService::STATUS_RETURNDED
                ]
            );
        }
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
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'disableIfRowIsDisabled' => true
        ),
    )
);

<?php

return array(
    'variables' => array(
        'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-tableHeader',
        'within_form' => true,
        'empty_message' => 'selfserve-app-subSection-previous-history-criminal-conviction-tableEmptyMessage'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array(
                    'label' => 'Add offence'
                ),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-columnName',
            'formatter' => 'Name',
            'type' => 'Action',
            'action' => 'edit'
        ),
        array(
            'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-columnDate',
            'name' => 'convictionDate',
            'formatter' => 'Date',
        ),
        array(
            'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-columnOffence',
            'name' => 'categoryText',
        ),
        array(
            'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-columnNameOfCourt',
            'name' => 'courtFpn',
        ),
        array(
            'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-columnPenalty',
            'name' => 'penalty',
        ),
        array(
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'type' => 'ActionLinks',
            'ariaDescription' => function ($row) {
                return $row['forename'] . ' ' . $row['familyName'] . ' ' .$row['categoryText'];
            },
            'deleteInputName' => 'data[table][action][delete][%d]',
        )
    )
);

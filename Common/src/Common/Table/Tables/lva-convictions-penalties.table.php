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
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'secondary', 'requireRows' => true)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => 'selfserve-app-subSection-previous-history-criminal-conviction-columnName',
            'value_format' => '{{title}} {{forename}} {{familyName}}',
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
            'width' => 'checkbox',
            'type' => 'Checkbox',
            'hideWhenDisabled' => true
        )
    )
);

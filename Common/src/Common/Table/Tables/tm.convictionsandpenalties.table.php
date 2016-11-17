<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.convictionsandpenalties.table',
        'empty_message' => 'transport-manager.convictionsandpenalties.table.empty',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-previous-conviction' => array(
                    'label' => 'transport-manager.convictionsandpenalties.table.add',
                    'class' => 'primary'
                ),
            )
        ),
    ),
    'columns' => array(
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.conviction-date',
            'name' => 'convictionDate',
            'formatter' => 'Date',
            'type' => 'Action',
            'action' => 'edit-previous-conviction'
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.offence',
            'name' => 'categoryText',
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.name-of-court',
            'name' => 'courtFpn',
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.penalty',
            'name' => 'penalty',
        ),
        array(
            'type' => 'ActionLinks',
            'deleteInputName' => 'convictions[action][delete-previous-conviction][%d]'
        )
    )
);

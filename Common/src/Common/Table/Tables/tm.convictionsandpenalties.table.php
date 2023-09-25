<?php

use Common\Service\Table\Formatter\Date;

return array(
    'variables' => array(
        'empty_message' => false,
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-previous-conviction' => array(
                    'label' => 'transport-manager.convictionsandpenalties.table.add',
                ),
            )
        ),
    ),
    'columns' => array(
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.conviction-date',
            'name' => 'convictionDate',
            'formatter' => Date::class,
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
            'title' => 'markup-table-th-remove', //this is a view partial from olcs-common
            'ariaDescription' => 'categoryText',
            'type' => 'ActionLinks',
            'deleteInputName' => 'convictions[action][delete-previous-conviction][%d]'
        )
    )
);

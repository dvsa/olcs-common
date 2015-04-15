<?php

return array(
    'variables' => array(
        'title' => 'transport-manager.convictionsandpenalties.table',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add-previous-conviction' => array('label' => 'Add', 'class' => 'primary'),
                'edit-previous-conviction' => array('label' => 'Edit', 'class' => 'secondary', 'requireRows' => true),
                'delete-previous-conviction' =>
                    array('label' => 'Remove', 'class' => 'secondary', 'requireRows' => true)
            )
        ),
    ),
    'columns' => array(
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.offence',
            'name' => 'categoryText',
            'formatter' => function ($row) {
                $routeParams = ['id' => $row['id'], 'action' => 'edit-previous-conviction'];
                $url = $this->generateUrl($routeParams);
                return '<a href="' . $url . '" class=js-modal-ajax>' . $row['categoryText'] . '</a>';
            },
        ),
        array(
            'title' => 'transport-manager.convictionsandpenalties.table.conviction-date',
            'name' => 'convictionDate',
            'formatter' => 'Date'
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
            'title' => '',
            'width' => 'checkbox',
            'type' => 'Checkbox'
        ),
    )
);

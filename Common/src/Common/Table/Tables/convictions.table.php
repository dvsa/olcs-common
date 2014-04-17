<?php

return array(
    'variables' => array(
        'title' => 'Convictions list'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'Dealt with' => array('requireRows' => true),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'warning', 'requireRows' => true)
            )
        ),
        'paginate' => array(
            'limit' => array(
                'default' => 10,
                'options' => array(10, 25, 50)
            )
        )
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => '',
            'width' => 'checkbox',
            'format' => '{{[elements/radio]}}'
        ),
        array(
            'title' => 'Date of conviction',
            'formatter' => function($data) {
                return date('d/m/Y', strtotime($data['dateOfConviction']));
            }
        ),
        array(
            'title' => 'Date of offence',
            'formatter' => function($data) {
                return date('d/m/Y', strtotime($data['dateOfOffence']));
            }
        ),
        array(
            'title' => 'Name / defendant type',
            'formatter' => function($data) {
                $person = $data['personFirstname'] . ' ' . $data['personLastname'];
                $organisationName = $data['operatorName'];
                return ($organisationName == '') ? $person : $organisationName;
            }
        ),
        array(
            'title' => 'Description',
            'name' => 'description'
        ),
        array(
            'title' => 'Court/FPN',
            'name' => 'courtFpm'
        ),
        array(
            'title' => 'Penalty',
            'name' => 'penalty'
        ),
        array(
            'title' => 'SI',
            'name' => 'si'
        ),
        array(
            'title' => 'Declared',
            'name' => 'decToTc'
        ),
        array(
            'title' => 'Dealt with',
            'name' => 'dealtWith'
        )
    )
);

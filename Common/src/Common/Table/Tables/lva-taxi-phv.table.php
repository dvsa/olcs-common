<?php

$translationPrefix = 'application_taxi-phv_licence.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'empty_message' => $translationPrefix . '.empty_message',
        'required_label' => 'licence',
        'within_form' => true
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

    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.licence-number',
            'class' => 'action--tertiary',
            'action' => 'edit',
            'name' => 'privateHireLicenceNo',
            'type' => 'Action'
        ),
        array(
            'title' => $translationPrefix . '.council',
            'name' => 'councilName'
        ),
        array(
            'title' => $translationPrefix . '.address',
            'formatter' => 'Address',
            'name' => 'address'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

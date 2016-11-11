<?php

return [
    'variables' => [
        'title' => 'lva.contact-details.phone-contact.table.title',
        'empty_message' => 'lva.contact-details.phone-contact.table.emptyMessage',
        'within_form' => true,
    ],
    'settings' => [
        'crud' => [
            'actions' => [
                'add' => [
                    'class' => 'tertiary large',
                    'label' => 'lva.contact-details.phone-contact.table.action.add'
                ],
            ],
        ],
    ],
    'columns' => [
        [
            'title' => 'lva.contact-details.phone-contact.table.col.type.title',
            'type' => 'Action',
            'action' => 'edit',
            'name' => 'phoneContactType->description',
            'width' => '40%',
            'formatter' => 'Translate',
        ],
        [
            'title' => 'lva.contact-details.phone-contact.table.col.number.title',
            'name' => 'phoneNumber',
            'width' => '50%',
        ],
        [
            'type' => 'ActionLinks',
            'width' => '10%',
        ],
    ],
];

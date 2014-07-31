<?php

return [
    'name' => 'defendant',
    'type' => '\Common\Form\Elements\Types\Person',
    'elements' => [
        'defType' => [
                        'type' => 'select',
                        'label' => 'Defendant type',
                        'value_options' => 'defendant_types'
                    ],
        'id' => [
            'type' => 'hidden'
        ],
        'version' => [
            'type' => 'hidden'
        ],
        'searchPerson' => [
            'type' => 'person-search',
            'label' => 'Search for person'
        ],
        'personFirstname' => [
            'type' => 'personName',
            'label' => 'First name',
             'class' => 'long'
        ],
        'personLastname' => [
            'type' => 'personName',
            'label' => 'Last name',
            'class' => 'long'
        ],
        'dateOfBirth' => [
             'type' => 'dateSelectWithEmpty',
             'label' => 'Date of birth',
             'filters' => '\Common\Form\Elements\InputFilters\DateNotRequiredNotInFuture'
         ]
    ]
];

<?php

return [
    'name' => 'person',
    'type' => '\Common\Form\Elements\Types\Person',
    'elements' => [
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
            'label' => 'First name(s)',
            'class' => 'long'
        ],
        'personLastname' => [
            'type' => 'personName',
            'label' => 'Last name',
            'class' => 'long'
        ],
        'birthDate' => [
             'type' => 'dateSelectWithEmpty',
             'label' => 'Date of birth',
             'filters' => '\Common\Form\Elements\InputFilters\DateNotRequiredNotInFuture'
         ]
    ]
];

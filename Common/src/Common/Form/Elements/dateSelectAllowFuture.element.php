<?php

return [
    'dateSelectAllowFuture' => [
        'type' => 'Common\Form\Elements\Custom\DateSelect',
        'name' => '',
        'options' => [
            'create_empty_option' => true,
            'render_delimiters' => false,
            'required' => true,
            'max_year_delta' => '+10',
        ]
    ]
];

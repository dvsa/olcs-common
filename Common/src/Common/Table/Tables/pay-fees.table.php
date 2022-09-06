<?php

return array(
    'variables' => array(
        'title' => 'pay-fees.table.title',
    ),
    'settings' => array(
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'pay-fees.description',
            'name' => 'description',
        ),
        array(
            'title' => 'pay-fees.reference',
            'formatter' => function ($row, $col, $sm) {
                return $row['licence']['licNo'];
            },
        ),
        array(
            'title' => 'pay-fees.amount',
            'isNumeric' => true,
            'name' => 'amount',
            'formatter' => 'FeeAmount',
        ),
        array(
            'title' => 'pay-fees.outstanding',
            'isNumeric' => true,
            'name' => 'outstanding',
            'formatter' => 'FeeAmount',
        ),
    ),
    'footer' => array(
        'total' => array(
            'type' => 'th',
            'content' => 'dashboard-fees-total',
            'formatter' => 'Translate',
            'colspan' => 3,
        ),
        array(
            'type' => 'th',
            'formatter' => 'FeeAmountSum',
            'name' => 'outstanding',
            'align' => 'govuk-!-text-align-right',
        ),
    ),
);

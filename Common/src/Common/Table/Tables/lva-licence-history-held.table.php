<?php
$prefix = 'selfserve-app-subSection-previous-history-previous-licence-';

return array(
    'variables' => array(
        'title' => $prefix . 'tableHeader',
        'within_form' => true,
        'empty_message' => $prefix . 'tableEmptyMessage'
    ),
    'settings' => array(
        'crud' => array(
            'formName' => 'held',
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'secondary', 'requireRows' => true)
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $prefix . 'columnLicNo',
            'name' => 'licNo',
            'type' => 'Action',
            'class' => 'action--tertiary',
            'action' => 'edit'
        ),
        array(
            'title' => $prefix . 'columnHolderName',
            'name' => 'holderName',
        ),
        array(
            'title' => $prefix . 'columnPurchaseDate',
            'name' => 'purchaseDate',
            'formatter' => 'Date'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox'
        )
    )
);

<?php
use Common\Controller\Lva\AbstractSafetyController;
use Common\Service\Table\Formatter\Address;
use Common\Service\Table\Formatter\StackValue;
use Common\Service\Table\Formatter\YesNo;

$translationPrefix = 'safety-inspection-providers.table';

return array(
    'variables' => array(
        'title' => $translationPrefix . '.title',
        'empty_message' => $translationPrefix . '.hint',
        'required_label' => 'safety inspection provider',
        'within_form' => true
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Add safety inspector'),
            )
        ),
        'paginate' => array(
            'limit' => array(
                'default' => AbstractSafetyController::DEFAULT_TABLE_RECORDS_COUNT,
                'options' => array(10, 25, 50),
            ),
        ),
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.providerName',
            'action' => 'edit',
            'stack' => 'contactDetails->fao',
            'formatter' => StackValue::class,
            'type' => 'Action',
            'keepForReadOnly' => true,
        ),
        array(
            'title' => $translationPrefix . '.external',
            'name' => 'isExternal',
            'formatter' => YesNo::class
        ),
        array(
            'title' => $translationPrefix . '.address',
            'formatter' => Address::class,
            'name' => 'contactDetails->address'
        ),
        array(
            'title' => 'markup-table-th-remove',
            'ariaDescription' => function ($row) {
                return $row['contactDetails']['fao'] ?? 'safety inspector';
            },
            'type' => 'ActionLinks',
        ),
    )
);

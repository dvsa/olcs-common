<?php
use Common\Controller\Lva\AbstractSafetyController;

$translationPrefix = 'safety-inspection-providers.table';

return array(
    'variables' => array(
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
            'formatter' => 'StackValue',
            'type' => 'Action',
        ),
        array(
            'title' => $translationPrefix . '.external',
            'name' => 'isExternal',
            'formatter' => 'YesNo'
        ),
        array(
            'title' => $translationPrefix . '.address',
            'formatter' => 'Address',
            'name' => 'contactDetails->address'
        ),
        array(
            'title' => 'markup-table-th-remove',
            'type' => 'ActionLinks',
        ),
    )
);

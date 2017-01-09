<?php

$translationPrefix = 'psv_discs.table';

return array(
    'variables' => array(
        'title' => '',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Request new discs', 'class' => 'primary'),
                'replace' => array(
                    'label' => 'Replace',
                    'class' => 'secondary js-require--multiple',
                    'requireRows' => true
                ),
                'void' => array(
                    'label' => 'Remove',
                    'class' => 'secondary js-require--multiple',
                    'requireRows' => true
                ),
            )
        ),
        'paginate' => array(
            'limit' => array(
                'default' => 10,
                'options' => array(10, 25, 50)
            )
        ),
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
        'collapseAt' => 1
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.discNo',
            'name' => 'discNo'
        ),
        array(
            'formatter' => 'DateTime',
            'title' => $translationPrefix . '.issuedDate',
            'name' => 'issuedDate'
        ),
        array(
            'formatter' => 'DateTime',
            'title' => $translationPrefix . '.ceasedDate',
            'name' => 'ceasedDate'
        ),
        array(
            'title' => $translationPrefix . '.replacement',
            'name' => 'isCopy',
            'formatter' => 'YesNo'
        ),
        array(
            'type' => 'ActionLinks',
            'isRemoveVisible' => function ($data) {
                return empty($data['ceasedDate']);
            },
            'isReplaceVisible' => function ($data) {
                return empty($data['ceasedDate']);
            },
            'deleteInputName' => 'table[action][void][%d]',
            'replaceInputName' => 'table[action][replace][%d]'
        ),
        array(
            'width' => 'checkbox',
            'type' => 'Checkbox',
        )
    )
);

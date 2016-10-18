<?php

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
                'add' => array(
                    'class' => 'primary', 
                    'label' => 'Add safety inspector'
                ),
            )
        )
    ),
    'columns' => array(
        array(
            'title' => $translationPrefix . '.providerName',
            'action' => 'edit',
            'stack' => 'contactDetails->fao',
            'formatter' => 'StackValue',
            'type' => 'Action'
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

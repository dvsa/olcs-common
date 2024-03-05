<?php

use Common\Service\Table\Formatter\Date;
use Common\Service\Table\Formatter\DisqualifyUrl;
use Common\Service\Table\Formatter\Name;
use Common\Service\Table\Formatter\YesNo;

return array(
    'variables' => array(
        'title' => 'selfserve-app-subSection-your-business-people-tableHeaderPeople',
        'empty_message' => 'selfserve-app-subSection-your-business-people-other.table.empty-message',
        'required_label' => 'person',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('label' => 'Add person'),
            )
        ),
        'row-disabled-callback' => function ($row) {
            return in_array($row['action'], ['D', 'C'], true);
        }
    ),
    'columns' => array(
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnName',
            'type' => 'VariationRecordAction',
            'action' => 'edit',
            'keepForReadOnly' => true,
            'formatter' => Name::class
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnHasOtherNames',
            'name' => 'otherName',
            'formatter' => YesNo::class,
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnDate',
            'name' => 'birthDate',
            'formatter' => Date::class,
        ),
        array(
            'title' => 'Disqual',
            'name' => 'disqual',
            'formatter' => DisqualifyUrl::class
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnPosition',
            'name' => 'position',
        ),
        array(
            'title' => 'markup-table-th-remove-restore', //view partial from olcs-common
            'ariaDescription' => function ($row, $column) {
                $column['formatter'] = Name::class;
                return $this->callFormatter($column, $row['name']);
            },
            'type' => 'DeltaActionLinks',
        ),
    )
);

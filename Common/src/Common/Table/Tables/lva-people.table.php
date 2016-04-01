<?php

return array(
    'variables' => array(
        'title' => 'selfserve-app-subSection-your-business-people-tableHeaderPartners',
        'empty_message' => 'selfserve-app-subSection-your-business-people-tableEmptyMessage',
        'required_label' => 'person',
        'within_form' => true,
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary', 'label' => 'Add person'),
            )
        ),
        'actionFormat' => Common\Service\Table\TableBuilder::ACTION_FORMAT_BUTTONS,
        'collapseAt' => 1
    ),
    'columns' => array(
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnName',
            'type' => 'Action',
            'action' => 'edit',
            'formatter' => 'Name'
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnHasOtherNames',
            'name' => 'otherName',
            'formatter' => function ($row) {
                return ($row['otherName'] ? 'Yes' : 'No');
            }
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnDate',
            'name' => 'birthDate',
            'formatter' => 'Date',
        ),
        array(
            'title' => 'Disqual',
            'name' => 'disqual',
            'formatter' => function ($row) {
                return sprintf(
                    '<a href="%s" class="js-modal-ajax">%s</a>',
                    $this->generateUrl(array('child_id' => $row['id'], 'action' => 'disqualify')),
                    $row['disqualificationStatus']
                );
            }
        ),
        array(
            'title' => 'selfserve-app-subSection-your-business-people-columnPosition',
            'name' => 'position',
        ),
        array(
            'type' => 'ActionLinks',
            'name' => 'actionLinks'
        ),
    )
);

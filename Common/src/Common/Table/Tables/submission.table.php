<?php

return array(
    'variables' => array(
        'title' => 'Submission list'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'warning', 'requireRows' => true)
            )
        ),
        'paginate' => array(
            'limit' => array(
                'default' => 10,
                'options' => array(10, 25, 50)
            )
        )
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => '',
            'width' => 'checkbox',
            'format' => '{{[elements/radio]}}'
        ),
        array(
            'title' => 'Submission #',
            'formatter' => function($row) {
                return '<a href="' . $this->generateUrl(array('case' => $row['id'], 'tab' => 'overview'), 'case_manage') . '">' . $row['submissionNumber'] . '</a>';
            }
        ),
        array(
            'title' => 'Sub status',
            'name' => 'createdOn'
        ),
        array(
            'title' => 'Date created',
            'name' => 'lastUpdatedOn'
        ),
        array(
            'title' => 'Currently with',
            'name' => 'with'
        ),
        array(
            'title' => 'Urgent',
            'name' => 'urgent'
        )
    )
);

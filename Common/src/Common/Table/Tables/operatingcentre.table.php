<?php

return array(
    'variables' => array(
        'title' => 'Operating centres'
    ),
    'settings' => array(
        'crud' => array(
            'actions' => array(
                'add' => array('class' => 'primary'),
                'edit' => array('requireRows' => true),
                'delete' => array('class' => 'warning', 'requireRows' => true)
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
            'title' => 'Operating Centre Address',
            'formatter' => function($data) {
                $parts = array();
                foreach (array('addressLine1', 'addressLine2', 'addressLine3', 'postcode') as $item) {
                    if (!empty($data['address'][$item])) {
                        $parts[] = $data['address'][$item];
                    }
                }

                return "<a href='#'>".implode(', ', $parts)."</a>";
            },
            'sort' => 'address',
            'name' => 'address'
        ),
        array(
            'title' => 'Vehicles',
            'format' => '{{vehicleAuth}}',
            'sort' => 'vehicleAuth'
        ),
        array(
            'title' => 'Trailers',
            'format' => '{{trailerAuth}}',
            'sort' => 'trailerAuth'
        ),
        array(
            'title' => 'Permission',
            'name' => 'permission'
        ),
        array(
            'title' => 'Advertising',
            'name' => 'advertising'
        ),
        array(
            'title' => 'Proof',
            'name' => 'proof'
        ),
    ),
    // Footer configuration
    'footer' => array(
        array(
            'type' => 'th',
            'format' => 'Total vehicles and trailers', // i.e. 'Title: {{title}}'
            'colspan' => 2
        ),
        array(
            'formatter' => 'Sum',
            'name' => 'vehicleAuth'
        ),
        array(
            'formatter' => 'Sum',
            'name' => 'trailerAuth'
        ),
        array(
            'colspan' => 3
        )
    )
);

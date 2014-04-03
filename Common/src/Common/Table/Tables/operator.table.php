<?php

return array(
    'settings' => array(
        'title' => 'Result list',
        'paginate' => true
    ),
    'attributes' => array(
    ),
    'columns' => array(
        array(
            'title' => 'Lic no/status',
            'format' => '<a href="#">{{licenceNumber}}</a><br/>{{status}}',
            'sort' => 'licenceNumber'
        ),
        array(
            'title' => 'App ID/status',
            'format' => '{{appNumber}}<br/>{{appStatus}}',
            'sort' => 'appId'
        ),
        array(
            'title' => 'Op/trading name',
            'formatter' => function($data) {
                return $data['trading_as'] ?: $data['name'];
            },
            'sort' => 'operatorName'
        ),
        array(
            'title' => 'Company/Lic type',
            'name' => 'licenceType'
        ),
        array(
            'title' => 'Last act CN/Date',
            'name' => 'last_updated_on',
            'formatter' => '_date',
            'sort' => 'lastActionDate'
        ),
        array(
            'title' => 'Correspondence address',
            'formatter' => function($data) {
                $parts = array();
                foreach (array('address_line1', 'address_line2', 'address_line3', 'postcode') as $item) {
                    if (!empty($data[$item])) {
                        $parts[] = $data[$item];
                    }
                }

                return implode(', ', $parts);
            },
            'sort' => 'correspondenceAddress'
        ),
        array(
            'title' => 'Cases',
            'formatter' => function($data) {
                if (isset($data['caseCount']) && (int)$data['caseCount'] > 0) {
                    return '<a href="/case/' . $data['licenceId'] . '">' . $data['caseCount'] . '</a>';
                } else {
                    return '<a href="/case/' . $data['licenceId'] . '/add">[Add Case]</a>';
                }
            }
        ),
        array(
            'title' => 'MLH',
            'format' => '[MLH]'
        ),
        array(
            'title' => 'Info',
            'format' => '[Info]'
        )
    )
);

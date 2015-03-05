<?php

/**
 * UnlinkedTm.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

class UnlinkedTm extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = array(
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'tmLicences' => [
                        'children' => [
                            'transportManager' => [
                                'children' => [
                                    'homeCd'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $query;
    }

    public function render()
    {
        $licences = $this->data['tmLicences'];

        $output = '';
        foreach ($licences as $licence) {
            $person = $licence['transportManager']['homeCd'];
            $output .= $person['forename'] . ' ' . $person['familyName'] . '\n';
        }

        return $output;
    }
}
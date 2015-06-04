<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * OpNameOnly bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OpNameOnly extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'organisation'
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        if (isset($this->data['organisation']['name'])) {
            return $this->data['organisation']['name'];
        }
        return '';
    }
}

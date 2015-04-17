<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Transport manager name bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'TransportManager',
            'data' => [
                'id' => $data['transportManager']
            ],
            'bundle' => [
                'children' => [
                    'homeCd' => [
                        'children' => [
                            'person'
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        return $this->data['homeCd']['person']['forename'] . ' ' . $this->data['homeCd']['person']['familyName'];
    }
}

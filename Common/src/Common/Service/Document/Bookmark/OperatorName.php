<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Operator name bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OperatorName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'properties' => ['organisation'],
                'children' => [
                    'organisation' => [
                        'properties' => ['name']
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        return $this->data['organisation']['name'];
    }
}

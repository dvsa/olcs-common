<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Operator 'FAO' bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OpFaoName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'correspondenceCd'
                ]
            ]
        ];
    }

    public function render()
    {
        return $this->data['correspondenceCd']['fao'];
    }
}

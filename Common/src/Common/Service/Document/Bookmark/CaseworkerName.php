<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Caseworker name bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CaseworkerName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'User',
            'data' => [
                'id' => $data['user']
            ],
            'bundle' => [
                'children' => [
                    'contactDetails' => [
                        'children' => ['person']
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        return Formatter\Name::format($this->data['contactDetails']['person']);
    }
}

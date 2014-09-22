<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

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
                'properties' => ['contactDetails'],
                'children' => [
                    'contactDetails' => [
                        'properties' => ['forename', 'familyName']
                    ]
                ]
            ]
        ];
    }

    public function format()
    {
        return Formatter\Name::format($this->data['contactDetails']);
    }
}

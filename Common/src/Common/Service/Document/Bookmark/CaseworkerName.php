<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

class CaseworkerName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'User',
            'data' => [
                'id' => $data['user']
            ],
            'bundle' => [
                'properties' => ['name']
            ]
        ];

        return $query;
    }

    public function format()
    {
        return $this->data['name'];
    }
}

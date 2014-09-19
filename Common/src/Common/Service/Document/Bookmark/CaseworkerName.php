<?php
namespace Common\Service\Document\Bookmark;

class CaseworkerName extends AbstractBookmark
{
    public function getQuery($data)
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

    public function format($data)
    {
        return $data['name'];
    }
}

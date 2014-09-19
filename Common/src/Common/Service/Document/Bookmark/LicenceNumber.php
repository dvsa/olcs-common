<?php
namespace Common\Service\Document\Bookmark;

class LicenceNumber extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'properties' => ['licNo']
            ]
        ];

        return $query;
    }

    public function format()
    {
        return $this->data['licNo'];
    }
}

<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

class LicenceHolderName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
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

        return $query;
    }

    public function format()
    {
        return $this->data['organisation']['name'];
    }
}

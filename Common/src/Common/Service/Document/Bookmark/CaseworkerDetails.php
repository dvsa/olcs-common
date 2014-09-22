<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

class CaseworkerDetails extends DynamicBookmark
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
        // @TODO need more data, e.g. address, plus needs to be newline
        // separated
        return $this->data['name'] . "\n" . "Address 1";
    }
}

<?php
namespace Common\Service\Document\Bookmark;

class CaseworkerDetails extends AbstractBookmark
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
        $details = $data[$this->token];
        // @TODO need more data, e.g. address, plus needs to be newline
        // separated
        return $details['name'] . "\n" . "Address 1";
    }
}

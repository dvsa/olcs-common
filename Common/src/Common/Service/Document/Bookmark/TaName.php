<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Traffic Area Name bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'trafficArea'
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        return $this->data['trafficArea']['name'];
    }
}

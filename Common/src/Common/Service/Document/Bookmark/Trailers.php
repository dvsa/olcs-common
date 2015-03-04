<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Licence - Total trailers authority
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Trailers extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => []
        ];

        return $query;
    }

    public function render()
    {
        return $this->data['totAuthTrailers'];
    }
}

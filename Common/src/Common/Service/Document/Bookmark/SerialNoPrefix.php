<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Serial number prefix bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class SerialNoPrefix extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'CommunityLic',
            'data' => [
                'id' => $data['communityLic']
            ],
            'bundle' => []
        ];

        return $query;
    }

    public function render()
    {
        return $this->data['serialNoPrefix'];
    }
}

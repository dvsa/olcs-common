<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Community Licence - Valid From
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateFrom extends DynamicBookmark
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
        // @TODO confirm with Steve L, AC says issued date
        return date("d/m/Y", strtotime($this->data['specifiedDate']));
    }
}

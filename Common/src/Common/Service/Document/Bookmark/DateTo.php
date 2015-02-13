<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Community Licence - Valid To
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateTo extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'CommunityLic',
            'data' => [
                'id' => $data['communityLic']
            ],
            'bundle' => [
                'children' => [
                    'licence'
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        return date("d/m/Y", strtotime($this->data['licence']['expiryDate']));
    }
}

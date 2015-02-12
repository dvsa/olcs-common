<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Original Copy bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OriginalCopy extends DynamicBookmark
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
        if ($this->data['issueNo'] === 0) {
            return 'LICENCE';
        }
        return 'CERTIFIED TRUE COPY';
    }
}

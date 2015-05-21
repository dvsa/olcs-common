<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * InsMoreFreqNo bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsMoreFreqNo extends DynamicBookmark
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
        if (!$this->data['safetyInsVaries']) {
            return 'X';
        }
        return '';
    }
}

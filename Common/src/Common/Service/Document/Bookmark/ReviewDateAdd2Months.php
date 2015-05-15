<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * ReviewDateAdd2Month bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ReviewDateAdd2Months extends DynamicBookmark
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
        if (isset($this->data['reviewDate'])) {
            $target = new \DateTime($this->data['reviewDate']);
            $target->add(new \DateInterval('P2M'));
            return $target->format('d/m/Y');
        }
        return '';
    }
}

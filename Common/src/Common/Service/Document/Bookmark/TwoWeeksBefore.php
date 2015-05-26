<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Two Weeks Before bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TwoWeeksBefore extends DynamicBookmark
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
        if (isset($this->data['expiryDate'])) {
            $target = new \DateTime($this->data['expiryDate']);
            $interval = new \DateInterval('P14D');
            $interval->invert = 1;
            $target->add($interval);
            return $target->format('d/m/Y');
        }
        return '';
    }
}

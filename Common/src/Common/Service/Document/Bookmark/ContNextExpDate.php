<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Continuation Next Expiry Date bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ContNextExpDate extends DynamicBookmark
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
            $target->add(new \DateInterval('P5Y'));
            return $target->format('d/m/Y');
        }
        return '';
    }
}

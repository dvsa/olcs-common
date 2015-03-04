<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Licence - Review Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ReviewDate extends DynamicBookmark
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
        return date('d/m/Y', strtotime($this->data['reviewDate']));
    }
}

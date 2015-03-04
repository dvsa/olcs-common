<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Licence - In Force Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class IssueDate extends DynamicBookmark
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
        return date("d/m/Y", strtotime($this->data['inForceDate']));
    }
}

<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Licence - Conditions
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Conditions extends DynamicBookmark
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
        // @TODO
    }
}

<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Licence - count of discs
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscsIssued extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'psvDiscs'
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        $activeDiscs = array_filter(
            $this->data['psvDiscs'],
            function ($val) {
                return $val['ceasedDate'] === null;
            }
        );
        return count($activeDiscs);
    }
}

<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Trading names bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TradingNames extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = array(
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'organisation' => [
                        'children' => [
                            'tradingNames'
                        ]
                    ]
                ]
            ]
        );

        return $query;
    }

    public function render()
    {
        if (isset($this->data['organisation']['tradingNames'])) {
            return implode(', ', $this->data['organisation']['tradingNames']);
        }
        return '';
    }
}

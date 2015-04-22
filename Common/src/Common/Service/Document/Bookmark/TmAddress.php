<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Transport manager address bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmAddress extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'TransportManager',
            'data' => [
                'id' => $data['transportManager']
            ],
            'bundle' => [
                'children' => [
                    'homeCd' => [
                        'children' => [
                            'address'
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        $formatter = new Formatter\Address();
        $formatter->setSeparator(', ');
        return $formatter->format($this->data['homeCd']['address']);
    }
}

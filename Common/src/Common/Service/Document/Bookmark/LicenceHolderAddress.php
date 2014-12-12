<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Licence holder address bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceHolderAddress extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'organisation' => [
                        'children' => [
                            'contactDetails' => [
                                'children' => [
                                    'address'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        return Formatter\Address::format($this->data['organisation']['contactDetails']['address']);
    }
}

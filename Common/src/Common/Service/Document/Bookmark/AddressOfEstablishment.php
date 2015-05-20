<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Address of Establishment bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AddressOfEstablishment extends DynamicBookmark
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
                    'establishmentCd' => [
                        'children' => [
                            'address'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        if (isset($this->data['establishmentCd']['address'])) {
            return Formatter\Address::format($this->data['establishmentCd']['address']);
        }
        return '';
    }
}

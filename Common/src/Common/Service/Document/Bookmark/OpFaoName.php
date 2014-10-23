<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Operator 'FAO' bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OpFaoName extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'properties' => ['organisation'],
                'children' => [
                    'organisation' => [
                        'properties' => ['contactDetails'],
                        'children' => [
                            'contactDetails' => [
                                'properties' => ['contactType', 'fao'],
                                'children' => [
                                    'contactType' => [
                                        'properties' => ['id']
                                    ]
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
        foreach ($this->data['organisation']['contactDetails'] as $contactDetail) {
            if ($contactDetail['contactType']['id'] === 'ct_corr') {
                return $contactDetail['fao'];
            }
        }
    }
}

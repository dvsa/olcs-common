<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

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
                'properties' => ['organisation'],
                'children' => [
                    'organisation' => [
                        'properties' => ['contactDetails'],
                        'children' => [
                            'contactDetails' => [
                                'properties' => ['contactType', 'address'],
                                'children' => [
                                    'contactType' => [
                                        'properties' => ['id']
                                    ],
                                    'address' => [
                                        'properties' => [
                                            'addressLine1',
                                            'addressLine2',
                                            'addressLine3',
                                            'addressLine4',
                                            'town',
                                            'postcode'
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function format()
    {
        foreach ($this->data['organisation']['contactDetails'] as $contactDetail) {
            if ($contactDetail['contactType']['id'] === 'ct_corr') {
                $address = $contactDetail['address'];

                return $address['addressLine1']
                    . "\n" . $address['addressLine2']
                    . "\n" . $address['addressLine3']
                    . "\n" . $address['addressLine4']
                    . "\n" . $address['town']
                    . "\n" . $address['postcode'];
            }
        }
    }
}

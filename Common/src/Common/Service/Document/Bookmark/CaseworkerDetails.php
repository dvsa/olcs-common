<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

class CaseworkerDetails extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return [
            'service' => 'User',
            'data' => [
                'id' => $data['user']
            ],
            'bundle' => [
                'properties' => [
                    'team',
                    'contactDetails'
                ],
                'children' => [
                    'contactDetails' => [
                        'properties' => [
                            'forename',
                            'familyName',
                            'emailAddress',
                            'address'
                        ],
                        'children' => [
                            /**
                             * 1) Preferred address; directly linked against a user
                             */
                            'address' => [
                                'properties' => [
                                    'addressLine1',
                                    'addressLine2',
                                    'addressLine3',
                                    'addressLine4',
                                    'town',
                                    'postcode'
                                ]
                            ]
                        ]
                    ],
                    'team' => [
                        'properties' => ['trafficArea'],
                        'children' => [
                            'trafficArea' => [
                                'properties' => ['name', 'contactDetails'],
                                'children' => [
                                    'contactDetails' => [
                                        'properties' => ['address'],
                                        'children' => [
                                            /**
                                             * 2) Fallback address; linked traffic area
                                             */
                                            'address' => [
                                                'properties' => [
                                                    'addressLine1',
                                                    'addressLine2',
                                                    'addressLine3',
                                                    'addressLine4',
                                                    'town',
                                                    'postcode'
                                                ]
                                            ]
                                        ]
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
        if (!empty($this->data['contactDetails']['address'])) {
            $address = $this->data['contactDetails']['address'];
        } else {
            $address = $this->data['team']['trafficArea']['contactDetails']['address'];
        }

        return implode(
            "\n",
            [
                Formatter\Name::format($this->data['contactDetails']),
                Formatter\Address::format($address)
            ]
        );
    }
}

<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Caseworker details bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
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

    public function render()
    {
        if (!empty($this->data['contactDetails']['address'])) {
            $address = $this->data['contactDetails']['address'];
        } else {
            $address = $this->data['team']['trafficArea']['contactDetails']['address'];
        }

        $taName = isset($this->data['team']['trafficArea']['name'])
            ? $this->data['team']['trafficArea']['name']
            : '';

        return implode(
            "\n",
            array_filter(
                [
                    Formatter\Name::format($this->data['contactDetails']),
                    $taName,
                    Formatter\Address::format($address),
                    'Direct Line:' . '', // @TODO pending based on forthcoming discussions with Paul Roberts RE schema
                    'e-mail: ' . $this->data['contactDetails']['emailAddress']
                ]
            )
        );
    }
}

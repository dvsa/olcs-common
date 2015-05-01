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
    // makes our ref data key a bit clearer in context
    const TEL_DIRECT_DIAL ='phone_t_tel';

    public function getQuery(array $data)
    {
        return [
            'service' => 'User',
            'data' => [
                'id' => $data['user']
            ],
            'bundle' => [
                'children' => [
                    'contactDetails' => [
                        'children' => [
                            /**
                             * 1) Preferred address; directly linked against a user
                             */
                            'address' => [],
                            'phoneContacts' => [
                                'children' => [
                                    'phoneContactType'
                                ]
                            ],
                            'person'
                        ]
                    ],
                    'team' => [
                        'children' => [
                            'trafficArea' => [
                                'children' => [
                                    'contactDetails' => [
                                        'children' => [
                                            /**
                                             * 2) Fallback address; linked traffic area
                                             */
                                            'address'
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
        $directDial = $this->fetchDirectDial();

        $address = $this->fetchBestAddress();

        $taName = isset($this->data['team']['trafficArea']['name'])
            ? $this->data['team']['trafficArea']['name']
            : '';

        $details = $this->data['contactDetails'];

        return implode(
            "\n",
            array_filter(
                [
                    Formatter\Name::format($details['person']),
                    $this->data['jobTitle'],
                    $this->data['divisionGroup'],
                    $this->data['departmentName'],
                    $taName,
                    Formatter\Address::format($address),
                    'Direct Line: ' . $directDial,
                    'e-mail: ' . $details['emailAddress']
                ]
            )
        );
    }

    private function fetchBestAddress()
    {
        // we prefer an address directly linked against the user...
        if (!empty($this->data['contactDetails']['address'])) {
            return $this->data['contactDetails']['address'];
        }

        // but if not, fall back to the one against the team's TA
        return $this->data['team']['trafficArea']['contactDetails']['address'];
    }

    private function fetchDirectDial()
    {
        if (empty($this->data['contactDetails']['phoneContacts'])) {
            return '';
        }
        foreach ($this->data['contactDetails']['phoneContacts'] as $phone) {
            if ($phone['phoneContactType']['id'] === self::TEL_DIRECT_DIAL) {
                return $phone['phoneNumber'];
            }
        }
    }
}

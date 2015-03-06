<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\PhoneContactEntityService;

/**
 * Traffic Area (with phone number) Address bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaAddressPhone extends DynamicBookmark
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
                    'trafficArea' => [
                        'children' => [
                            'contactDetails' => [
                                'children' => [
                                    'address',
                                    'phoneContacts' => [
                                        'children' => [
                                            'phoneContactType'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        $trafficArea = $this->data['trafficArea'];
        $contactDetails = $trafficArea['contactDetails'];
        $address = isset($contactDetails['address']) ? $contactDetails['address'] : [];

        return implode(
            "\n",
            array_filter(
                [
                    $trafficArea['name'],
                    Formatter\Address::format($address),
                    $this->fetchTelephone()
                ]
            )
        );
    }

    private function fetchTelephone()
    {
        if (empty($this->data['trafficArea']['contactDetails']['phoneContacts'])) {
            return '';
        }

        foreach ($this->data['trafficArea']['contactDetails']['phoneContacts'] as $phone) {
            if ($phone['phoneContactType']['id'] === PhoneContactEntityService::TYPE_BUSINESS) {
                return $phone['phoneNumber'];
            }
        }
    }
}

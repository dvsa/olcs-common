<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

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
                                    'phoneContacts'
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
        // @TODO this is always set; we need to pick out the best row
        $phone = isset($contactDetails['phoneContacts']) ? $contactDetails['phoneContacts']['phoneNumber'] : null;

        return implode(
            "\n",
            array_filter(
                [
                    $trafficArea['name'],
                    Formatter\Address::format($address),
                    $phone
                ]
            )
        );
    }
}

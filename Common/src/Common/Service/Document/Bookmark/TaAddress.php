<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Traffic Area Address bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaAddress extends DynamicBookmark
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
                                    'address'
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
        $address = isset($trafficArea['contactDetails']['address']) ? $trafficArea['contactDetails']['address'] : [];

        return implode(
            "\n",
            array_filter(
                [
                    $trafficArea['name'],
                    Formatter\Address::format($address)
                ]
            )
        );
    }
}

<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Company trading name bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CompanyTradingName extends DynamicBookmark
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
                            ],
                            'tradingNames'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function render()
    {
        $organisation = $this->data['organisation'];
        $address = isset($organisation['contactDetails']['address']) ? $organisation['contactDetails']['address'] : [];

        if (count($organisation['tradingNames'])) {
            $tradingAs = 'T/A ' . $this->getFirstTradingName($organisation['tradingNames']);
        } else {
            $tradingAs = '';
        }

        return implode(
            "\n",
            array_filter(
                [
                    $organisation['name'],
                    Formatter\Address::format($address)
                ]
            )
        );
    }

    private function getFirstTradingName($tradingNames)
    {
        // @TODO: based on created date ASC
        return $tradingNames[0];
    }
}

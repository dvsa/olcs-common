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
                    $tradingAs,
                    Formatter\Address::format($address)
                ]
            )
        );
    }

    private function getFirstTradingName($tradingNames)
    {
        // we could use usort here, but we don't actually want to sort
        // the whole array; we just want the earliest created so a simple
        // loop is (probably) quicker
        $first = null;
        $name = null;
        foreach ($tradingNames as $tradingName) {
            // save a strtotime if it's pointless
            if ($first === null) {
                $name = $tradingName['name'];
                continue;
            }

            $current = strtotime($tradingName['createdOn']);
            if ($current < $first) {
                $first = $current;
                $name = $tradingName['name'];
            }
        }

        return $name;
    }
}

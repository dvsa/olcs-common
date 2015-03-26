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
                    'correspondenceCd' => [
                        'children' => [
                            'address'
                        ]
                    ],
                    'organisation' => [
                        'children' => [
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
        $address = isset($this->data['correspondenceCd']['address']) ? $this->data['correspondenceCd']['address'] : [];

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
            $current = strtotime($tradingName['createdOn']);
            if ($name === null || $current < $first) {
                $first = $current;
                $name = $tradingName['name'];
            }
        }

        return $name;
    }
}

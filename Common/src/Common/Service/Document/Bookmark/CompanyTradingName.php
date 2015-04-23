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
        $address = isset($this->data['correspondenceCd']['address']) ? $this->data['correspondenceCd']['address'] : [];

        $formatter = new Formatter\OrganisationName();
        $formatter->setSeparator("\n");

        return implode(
            "\n",
            array_filter(
                [
                    $formatter->format($this->data['organisation']),
                    Formatter\Address::format($address)
                ]
            )
        );
    }
}

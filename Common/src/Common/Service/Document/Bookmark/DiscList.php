<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Document\Parser\ParserFactory;

/**
 * Disc list bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DiscList extends DynamicBookmark
{
    /**
     * We have to split some fields if they exceed this length
     */
    const MAX_LINE_LENGTH = 23;

    /**
     * No disc content? No problem
     */
    const PLACEHOLDER = 'XXXXXXXXX';

    private $discBundle = [
        'properties' => [
            'id',
            'isCopy'
        ],
        'children' => [
            'licenceVehicle' => [
                'properties' => ['licence', 'vehicle'],
                'children' => [
                    'licence' => [
                        'properties' => [
                            'organisation',
                            'licNo',
                            'expiryDate'
                        ],
                        'children' => [
                            'organisation' => [
                                'properties' => [
                                    'name',
                                    'tradingNames'
                                ],
                                'children' => [
                                    'tradingNames' => [
                                        'properties' => ['name']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'vehicle' => [
                        'properties' => ['vrm']
                    ]
                ]
            ]
        ]
    ];

    public function getQuery(array $data)
    {
        $query = [];

        foreach ($data as $id) {
            $query[] = [
                'service' => 'GoodsDisc',
                'data' => [
                    'id' => $id
                ],
                'bundle' => $this->discBundle
            ];
        }

        return $query;
    }

    public function render()
    {
        foreach ($this->data as $key => $disc) {

            $licence = $disc['licenceVehicle']['licence'];
            $vehicle = $disc['licenceVehicle']['vehicle'];
            $organisation = $licence['organisation'];

            $orgParts = $this->splitString($organisation['name']);

            // we want all trading names as one comma separated array...
            $tradingNames = $this->implodeNames($organisation['tradingNames']);
            // ... before worrying about whether to split them into different bookmarks
            $tradingParts = $this->splitString($tradingNames);

            $index = ($key % 2) + 1;
            $prefix = 'DISC' . $index . '_';

            $discs[] = [
                $prefix . 'TITLE'       => $disc['isCopy'] === 'Y' ? 'COPY' : '',
                $prefix . 'DISC_NO'     => $disc['discNo'],
                $prefix . 'LINE1'      => isset($orgParts[0]) ? $orgParts[0] : '',
                $prefix . 'LINE2'      => isset($orgParts[1]) ? $orgParts[1] : '',
                $prefix . 'LINE3'      => isset($orgParts[2]) ? $orgParts[2] : '',
                $prefix . 'LINE4'      => isset($tradingParts[0]) ? $tradingParts[0] : '',
                $prefix . 'LINE5'      => isset($tradingParts[0]) ? $tradingParts[0] : '',
                $prefix . 'LICENCE_ID'  => $licence['licNo'],
                $prefix . 'VEHICLE_REG' => $vehicle['vrm'],
                $prefix . 'EXPIRY_DATE' => isset($licence['expiryDate']) ? $licence['expiryDate'] : 'N/A'
            ];
        }

        /**
         * We always want an even number of discs, even if we have to
         * fill the rest up with placeholders
         */
        if (count($discs) % 2 === 1) {
            $discs[] = [
                'DISC2_TITLE'       => self::PLACEHOLDER,
                'DISC2_DISC_NO'     => self::PLACEHOLDER,
                'DISC2_LINE1'      => self::PLACEHOLDER,
                'DISC2_LINE2'      => self::PLACEHOLDER,
                'DISC2_LINE3'      => self::PLACEHOLDER,
                'DISC2_LINE4'      => self::PLACEHOLDER,
                'DISC2_LINE5'      => self::PLACEHOLDER,
                'DISC2_LICENCE_ID'  => self::PLACEHOLDER,
                'DISC2_VEHICLE_REG' => self::PLACEHOLDER,
                'DISC2_EXPIRY_DATE' => self::PLACEHOLDER
            ];
        }

        $discGroups = [];
        for ($i = 0; $i < count($discs) / 2; $i++) {
            $discGroups[] = array_merge($discs[$i], $discs[$i+1]);
        }

        $str = '';
        $snippet = $this->getSnippet();
        $parser = $this->getParser();

        foreach ($discGroups as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }

        return $str;
    }

    private function splitString($str)
    {
        $max = ceil(strlen($str) / self::MAX_LINE_LENGTH);
        $parts = [];

        for ($i = 0; $i < $max; $i++) {
            $parts[] = substr($str, $i * self::MAX_LINE_LENGTH, self::MAX_LINE_LENGTH);
        }

        return $parts;
    }

    private function implodeNames($names)
    {
        return implode(
            ", ",
            array_map(
                function($val) {
                    return $val['name'];
                },
                $names
            )
        );
    }
}

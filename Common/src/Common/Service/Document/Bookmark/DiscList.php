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
    const PLACEHOLDER = 'XXXXXXXXXX';

    /**
     * Discs per page - any shortfall will be voided with placeholders
     */
    const PER_PAGE = 6;

    /**
     * Discs per row in a page
     */
    const PER_ROW = 2;

    const PREFORMATTED = true;

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
        if (empty($this->data)) {
            return '';
        }

        foreach ($this->data as $key => $disc) {

            $licence = $disc['licenceVehicle']['licence'];
            $vehicle = $disc['licenceVehicle']['vehicle'];
            $organisation = $licence['organisation'];

            // split the org over multiple lines if necessary
            $orgParts = $this->splitString($organisation['name']);

            // we want all trading names as one comma separated array...
            $tradingNames = $this->implodeNames($organisation['tradingNames']);
            // ... before worrying about whether to split them into different bookmark lines
            $tradingParts = $this->splitString($tradingNames);

            $prefix = $this->getPrefix($key);

            $discs[] = [
                $prefix . 'TITLE'       => $disc['isCopy'] === 'Y' ? 'COPY' : '',
                $prefix . 'DISC_NO'     => $disc['discNo'],
                $prefix . 'LINE1'       => isset($orgParts[0]) ? $orgParts[0] : '',
                $prefix . 'LINE2'       => isset($orgParts[1]) ? $orgParts[1] : '',
                $prefix . 'LINE3'       => isset($orgParts[2]) ? $orgParts[2] : '',
                $prefix . 'LINE4'       => isset($tradingParts[0]) ? $tradingParts[0] : '',
                $prefix . 'LINE5'       => isset($tradingParts[1]) ? $tradingParts[1] : '',
                $prefix . 'LICENCE_ID'  => $licence['licNo'],
                $prefix . 'VEHICLE_REG' => $vehicle['vrm'],
                $prefix . 'EXPIRY_DATE' => isset($licence['expiryDate']) ? $licence['expiryDate'] : 'N/A'
            ];
        }

        /**
         * We always want a full page of discs, even if we have to
         * fill the rest up with placeholders
         */
        while (($length = count($discs) % self::PER_PAGE) !== 0) {

            $prefix = $this->getPrefix($length);
            $discs[] = [
                $prefix . 'TITLE'       => self::PLACEHOLDER,
                $prefix . 'DISC_NO'     => self::PLACEHOLDER,
                $prefix . 'LINE1'       => self::PLACEHOLDER,
                $prefix . 'LINE2'       => self::PLACEHOLDER,
                $prefix . 'LINE3'       => self::PLACEHOLDER,
                $prefix . 'LINE4'       => self::PLACEHOLDER,
                $prefix . 'LINE5'       => self::PLACEHOLDER,
                $prefix . 'LICENCE_ID'  => self::PLACEHOLDER,
                $prefix . 'VEHICLE_REG' => self::PLACEHOLDER,
                $prefix . 'EXPIRY_DATE' => self::PLACEHOLDER
            ];
        }

        // bit ugly, but now we have to chunk the discs into N per row
        $discGroups = [];
        for ($i = 0; $i < count($discs); $i+= self::PER_ROW) {
            $discGroups[] = array_merge($discs[$i], $discs[$i+1]);
        }

        $snippet = $this->getSnippet();
        $parser  = $this->getParser();

        // at last, we can loop through each group and run a sub
        // replacement on its tokens
        $str = '';
        foreach ($discGroups as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }

    /**
     * Split a string into N array parts based on a predefined
     * constant max line length
     */
    private function splitString($str)
    {
        $len = self::MAX_LINE_LENGTH;
        $max = ceil(strlen($str) / $len);
        $parts = [];

        for ($i = 0; $i < $max; $i++) {
            $parts[] = substr($str, $i * $len, $len);
        }

        return $parts;
    }

    /*
     * Take an array of arrays with a name value and return them
     * as a comma separated string instead
     */
    private function implodeNames($names)
    {
        return implode(
            ", ",
            array_map(
                function ($val) {
                    return $val['name'];
                },
                $names
            )
        );
    }

    /**
     * Return either DISC1_ or DISC2_ based on a given index
     */
    private function getPrefix($index)
    {
        $prefix = ($index % 2) + 1;
        return 'DISC' . $prefix . '_';
    }
}

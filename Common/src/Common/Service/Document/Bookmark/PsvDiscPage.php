<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * PSV Disc Page bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PsvDiscPage extends DynamicBookmark
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
     * Let the parser know we've already formatted our content by the
     * time it has been rendered
     */
    const PREFORMATTED = true;

    private $discBundle = [
        'children' => [
            'licence' => [
                'children' => [
                    'organisation' => [
                    ]
                ]
            ],
        ]
    ];

    public function getQuery(array $data)
    {
        $query = [];

        foreach ($data as $id) {
            $query[] = [
                'service' => 'PsvDisc',
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

            $licence = $disc['licence'];
            $organisation = $licence['organisation'];

            // split the org over multiple lines if necessary
            $orgParts = $this->splitString($organisation['name']);

            $prefix = $this->getPrefix($key);

            $discs[] = [
                $prefix . 'TITLE'       => $disc['isCopy'] === 'Y' ? 'COPY' : '',
                $prefix . 'DISC_NO'     => $disc['discNo'],
                $prefix . 'LINE1'       => isset($orgParts[0]) ? $orgParts[0] : '',
                $prefix . 'LINE2'       => isset($orgParts[1]) ? $orgParts[1] : '',
                $prefix . 'LINE3'       => isset($orgParts[2]) ? $orgParts[2] : '',
                $prefix . 'LICENCE'     => $licence['licNo'],
                $prefix . 'VALID_DATE'  => isset($licence['grantedDate']) ? $licence['grantedDate'] : 'N/A',
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
                $prefix . 'LICENCE'     => self::PLACEHOLDER,
                $prefix . 'VALID_DATE'  => self::PLACEHOLDER,
                $prefix . 'EXPIRY_DATE' => self::PLACEHOLDER
            ];
        }

        // bit ugly, but now we have to chunk the discs into N per page
        $discGroups = [];
        for ($i = 0; $i < count($discs); $i+= self::PER_PAGE) {
            $pageDiscs = [];
            for ($j = 0; $j < self::PER_PAGE; $j++) {
                $pageDiscs = array_merge(
                    $pageDiscs,
                    $discs[$j]
                );
            }
            $discGroups[] = $pageDiscs;
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
        return str_split($str, self::MAX_LINE_LENGTH);
    }

    /**
     * Return either DISC1_ or DISC2_ based on a given index
     */
    private function getPrefix($index)
    {
        $prefix = ($index % self::PER_PAGE) + 1;
        return 'PSV' . $prefix . '_';
    }
}

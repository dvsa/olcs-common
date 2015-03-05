<?php

/**
 * OpName.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Class OpName
 *
 * @todo This
 *
 * @package Common\Service\Document\Bookmark
 *
 * @author Josh Curtis <josh.curtis@valtech.com>
 */
class OpName extends DynamicBookmark
{
    /**
     * Get the query, this query returns the operators details.
     *
     * @param array $data The licence data
     *
     * @return array The query array.
     */
    public function getQuery(array $data)
    {
        $query = array(
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'organisation' => [
                        'children' => [
                            'tradingNames',
                            'contactDetails' => [
                                'children' => [
                                    'address'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $query;
    }

    /**
     * Return the operators name, company name, trading name(s) and address.
     *
     * @return string The operators address.
     */
    public function render()
    {
        $organisation = $this->data['organisation'];

        $operator = $organisation['contactDetails'];

        $tradingNames = '';
        array_map(function($tradingName) use (&$tradingNames) {
            $tradingNames .= $tradingName['name'] . ' ';
        }, $organisation['tradingNames']);

        $output = '';
        $output .= 'For Attention of ' . $operator['fao'] . "\n";
        $output .= $organisation['name'] . "\n";
        $output .= 'T/A: ' . substr($tradingNames, 0, 40) . "\n";
        $output .= Formatter\Address::format($operator['address']);

        return $output;
    }
}

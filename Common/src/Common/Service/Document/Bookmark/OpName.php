<?php

/**
 * OpName.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Class OpName
 *
 * Returns the operators name and address and associated contact information.
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
        array_map(
            function ($tradingName) use (&$tradingNames) {
                $tradingNames .= $tradingName['name'] . ' ';
            },
            $organisation['tradingNames']
        );

        return implode(
            "\n",
            array_filter(
                [
                    $operator['fao'],
                    $organisation['name'],
                    'T/A: ' . substr($tradingNames, 0, 40),
                    Formatter\Address::format($operator['address'])
                ]
            )
        );
    }
}

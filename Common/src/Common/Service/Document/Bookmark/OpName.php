<?php

/**
 * OpName.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Class OpName
 *
 * Returns the operator's name and address and associated contact information.
 *
 * @package Common\Service\Document\Bookmark
 *
 * @author Josh Curtis <josh.curtis@valtech.co.k>
 * @author Nick Payne <nick.payne@valtech.co.k>
 */
class OpName extends DynamicBookmark
{
    /**
     * Get the query, this query returns the operator's details.
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
                            'tradingNames'
                        ]
                    ]
                ]
            ]
        );

        return $query;
    }

    /**
     * Return the operator's name, company name, trading name(s) and address.
     *
     * @return string The operator's address.
     */
    public function render()
    {
        $organisation = $this->data['organisation'];

        $tradingNames = '';
        array_map(
            function ($tradingName) use (&$tradingNames) {
                $tradingNames .= $tradingName['name'] . ' ';
            },
            $organisation['tradingNames']
        );

        if (strlen($tradingNames) > 0) {
            $tradingNames = substr($tradingNames, 0, -1);
            $tradingNames = 'T/A: ' . substr($tradingNames, 0, 40);
        }

        return implode(
            "\n",
            array_filter(
                [
                    $organisation['name'],
                    $tradingNames
                ]
            )
        );
    }
}

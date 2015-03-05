<?php

/**
 * LicenceType.php
 */

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Class LicenceType
 *
 * Concatenates the licence types and category descriptions together in order to generate
 * a full licence type string.
 *
 * @package Common\Service\Document\Bookmark
 *
 * @author Josh Curtis <josh.curtis@valtech.com>
 */
class LicenceType extends DynamicBookmark
{
    /**
     * Returns the bundle query to be used in the REST call to the backend.
     *
     * @param array $data Data to be used within the query.
     *
     * @return array The full query array.
     */
    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'goodsOrPsv',
                    'licenceType'
                ]
            ]
        ];

        return $query;
    }

    /**
     * The render method to be used for this bookmark. This method returns the
     * types and categories as one string.
     *
     * @return string
     */
    public function render()
    {
        $goodsOrPsvData = $this->data['goodsOrPsv'];
        $licenceTypeData = $this->data['licenceType'];

        return $goodsOrPsvData['description'] . " " . $licenceTypeData['description'];
    }
}

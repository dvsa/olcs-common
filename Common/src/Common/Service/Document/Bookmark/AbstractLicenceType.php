<?php

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
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractLicenceType extends DynamicBookmark
{
    const SERVICE = 'Licence';
    const DATA_KEY = 'licence';

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
            'service' => static::SERVICE,
            'data' => [
                'id' => $data[static::DATA_KEY]
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
     * types and categories as one string
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

<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;

/**
 * Operating Centres list bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class OperatingCentres extends DynamicBookmark
{
    /**
     * Let the parser know we've already formatted our content by the
     * time it has been rendered
     */
    const PREFORMATTED = true;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'operatingCentres' => [
                        'children' => [
                            'operatingCentre' => [
                                'children' => [
                                    'address',
                                    'conditionUndertakings'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        if (empty($this->data)) {
            return '';
        }

        $data = $this->data['operatingCentres'];

        foreach ($data $key => $oc) {

            // iterate over all the OC rows and fetch:
            // $oc['operatingCentre']['address']
            // $oc['operatingCentre']['noOfVehiclesRequired']
            // below sometimes not relevant based on PSV/Goods
            // $oc['operatingCentre']['noOfTrailersRequired']
            // below needs to be looped over itself to generate
            // a concatenated paragraph
            // $oc['operatingCentre']['conditionUndertakings']

        }

        $snippet = $this->getSnippet('OcTable');
        $parser  = $this->getParser();

        $str = '';
        foreach ($snippets as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }
}

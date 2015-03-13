<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ConditionUndertakingEntityService;

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
                                    'conditionUndertakings' => [
                                        'children' => [
                                            'conditionType',
                                            'attachedTo',
                                            'licence'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'goodsOrPsv'
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

        $isGoods = $this->data['goodsOrPsv']['id'] === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE;
        $rows = [];

        foreach ($this->data['operatingCentres'] as $licenceOc) {
            $oc = $licenceOc['operatingCentre'];

            $conditionsUndertakings = Formatter\ConditionsUndertakings::format(
                $this->filterConditionsUndertakings($oc['conditionUndertakings'], $this->data['id'])
            );

            $rows[] = [
                'TAB_OC_ADD' => Formatter\Address::format($oc['address']),
                'TAB_OC_VEH' => $licenceOc['noOfVehiclesRequired'],
                'TAB_TRAILER' => $isGoods ? 'Trailers' : '',
                'TAB_OC_TRAILER' => $isGoods ? $licenceOc['noOfTrailersRequired'] : '',
                'TAB_OC_CONDS_UNDERS' => $conditionsUndertakings
            ];
        }

        $snippet = $this->getSnippet('OcTable');
        $parser  = $this->getParser();

        $str = '';
        foreach ($rows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }

    private function filterConditionsUndertakings($input, $licenceId)
    {
        return array_filter(
            $input,
            function ($val) use ($licenceId) {
                return (
                    $val['attachedTo']['id'] === ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE
                    && $val['isFulfilled'] === 'N'
                    && $val['isDraft'] === 'N'
                    && isset($val['licence']['id'])
                    && $val['licence']['id'] === $licenceId
                );
            }
        );
    }
}

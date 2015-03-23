<?php

namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Interim Operating Centres list bookmark
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimOperatingCentres extends DynamicBookmark
{
    /**
     * Let the parser know we've already formatted our content by the
     * time it has been rendered
     */
    const PREFORMATTED = true;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Application',
            'data' => [
                'id' => $data['application']
            ],
            'bundle' => [
                'children' => [
                    'operatingCentres' => [
                        'criteria' => [
                            'isInterim' => true
                        ],
                        'children' => [
                            'operatingCentre' => [
                                'children' => [
                                    'address',
                                    'conditionUndertakings' => [
                                        'criteria' => [
                                            'isDraft' => false,
                                            'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE,
                                            'isFulfilled' => false
                                        ],
                                        'children' => [
                                            'conditionType',
                                            'attachedTo',
                                            'licence',
                                            'application',
                                            'licConditionVariation'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'goodsOrPsv',
                    'licence'
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

        foreach ($this->data['operatingCentres'] as $childOc) {
            $oc = $childOc['operatingCentre'];


            $conditionsUndertakings = Formatter\ConditionsUndertakings::format(
                $this->filterConditionsUndertakings(
                    $oc['conditionUndertakings'],
                    $this->data['id'],
                    $this->data['licence']['id']
                )
            );

            $rows[] = [
                'TAB_OC_ADD' => Formatter\Address::format($oc['address']),
                'TAB_OC_VEH' => $childOc['noOfVehiclesRequired'],
                'TAB_TRAILER' => $isGoods ? 'Trailers' : '',
                'TAB_OC_TRAILER' => $isGoods ? $childOc['noOfTrailersRequired'] : '',
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

    private function filterConditionsUndertakings($input, $applicationId, $licenceId)
    {
        $conditions = [];
        foreach ($input as $condition) {
            if (isset($condition['licConditionVariation']['id'])) {
                $key = $condition['licConditionVariation']['id'];
            } else {
                $key = $condition['id'];
            }
            $conditions[$key] = $condition;
        }

        return array_filter(
            $conditions,
            function ($val) use ($applicationId, $licenceId) {
                return (
                    // @NOTE: we can't add this to our criteria because otherwise we could
                    // get the original undeleted item back
                    $val['action'] !== 'D'
                    && (
                        (isset($val['licence']['id']) && $val['licence']['id'] === $licenceId)
                        || (isset($val['application']['id']) && $val['application']['id'] === $applicationId)
                    )
                );
            }
        );
    }
}

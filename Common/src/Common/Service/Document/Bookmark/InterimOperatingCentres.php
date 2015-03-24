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
                    'goodsOrPsv',
                    'licence',
                    'operatingCentres' => [
                        'criteria' => [
                            'isInterim' => true
                        ],
                        'children' => [
                            'operatingCentre' => [
                                'children' => [
                                    'address',
                                    'conditionUndertakings' => [
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
        $licenceConditions = $this->getIndexedData($input, 'licence');
        $applicationConditions = $this->getIndexedData($input, 'application');

        $conditions = [];
        foreach ($licenceConditions as $id => $condition) {
            if (isset($applicationConditions[$id])) {
                $condition = $applicationConditions[$id];
            }

            if (
                $condition['isFulfilled'] === 'N'
                && $condition['attachedTo']['id'] === ConditionUndertakingEntityService::ATTACHED_TO_OPERATING_CENTRE
                && $condition['action'] !== 'D'
                && (
                    /**
                     * We can't filter by (licence = ? OR application = ?) in our query since
                     * we don't know the licence ID ahead of time; we only know it
                     * based on the application we get back
                     */
                    (isset($condition['licence']['id']) && $condition['licence']['id'] === $licenceId)
                    || (isset($condition['application']['id']) && $condition['application']['id'] === $applicationId)
                )
            ) {
                $conditions[] = $condition;
            }
        }

        return $conditions;
    }

    private function getIndexedData($input, $type)
    {
        $final = [];
        foreach ($input as $condition) {
            /**
             * Wrong type; bail early
             */
            if (!isset($condition[$type]['id'])) {
                continue;
            }

            if (isset($condition['licConditionVariation']['id'])) {
                $key = $condition['licConditionVariation']['id'];
            } else {
                $key = $condition['id'];
            }
            $final[$key] = $condition;
        }
        return $final;
    }
}

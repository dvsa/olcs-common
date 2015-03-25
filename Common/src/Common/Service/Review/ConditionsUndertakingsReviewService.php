<?php

/**
 * Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\ConditionUndertakingEntityService as Condition;

/**
 * Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ConditionsUndertakingsReviewService extends AbstractReviewService
{
    public function getConfigFromData(array $data = array())
    {
        // noop
    }

    public function formatLicenceSubSection($list, $lva, $conditionOrUndertaking, $action)
    {
        return [
            'title' => $lva . '-review-conditions-undertakings-licence-' . $conditionOrUndertaking . '-' . $action,
            'mainItems' => [
                [
                    'multiItems' => [
                        [
                            [
                                'list' => $this->formatConditionsList($list)
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function formatOcSubSection($list, $lva, $conditionOrUndertaking, $action)
    {
        $mainItems = [];

        foreach ($list as $conditions) {
            $mainItems[] = [
                'header' => $this->formatShortAddress($conditions[0]['operatingCentre']['address']),
                'multiItems' => [
                    [
                        [
                            'list' => $this->formatConditionsList($conditions)
                        ]
                    ]
                ]
            ];
        }

        return [
            'title' => $lva . '-review-conditions-undertakings-oc-' . $conditionOrUndertaking . '-' . $action,
            'mainItems' => $mainItems
        ];
    }

    /**
     * Flatten the conditions into a single dimension array
     *
     * @param array $conditions
     * @return array
     */
    public function formatConditionsList($conditions)
    {
        $list = [];

        foreach ($conditions as $condition) {
            $list[] = $condition['notes'];
        }

        return $list;
    }

    /**
     * Split all conditions and undertakings into 4 lists
     *  - Licence conditions
     *  - Licence undertakings
     *  - Operating centre conditions
     *  - Operating centre undertakings
     *
     * @param array $data
     * @return array
     */
    public function splitUpConditionsAndUndertakings($data)
    {
        $licConds = $licUnds = $ocConds = $ocUnds = [];

        foreach ($data['conditionUndertakings'] as $condition) {
            // Decide which list to push onto
            switch (true) {
                case $this->isLicenceCondition($condition):
                    $licConds[$condition['action']][] = $condition;
                    break;
                case $this->isLicenceUndertaking($condition):
                    $licUnds[$condition['action']][] = $condition;
                    break;
                case $this->isOcCondition($condition):
                    $ocConds[$condition['action']][$condition['operatingCentre']['id']][] = $condition;
                    break;
                case $this->isOcUndertaking($condition):
                    $ocUnds[$condition['action']][$condition['operatingCentre']['id']][] = $condition;
            }
        }

        return [$licConds, $licUnds, $ocConds, $ocUnds];
    }

    protected function isLicenceCondition($condition)
    {
        return $condition['conditionType']['id'] === Condition::TYPE_CONDITION
            && $condition['attachedTo']['id'] === Condition::ATTACHED_TO_LICENCE;
    }

    protected function isLicenceUndertaking($condition)
    {
        return $condition['conditionType']['id'] === Condition::TYPE_UNDERTAKING
            && $condition['attachedTo']['id'] === Condition::ATTACHED_TO_LICENCE;
    }

    protected function isOcCondition($condition)
    {
        return $condition['conditionType']['id'] === Condition::TYPE_CONDITION
            && $condition['attachedTo']['id'] === Condition::ATTACHED_TO_OPERATING_CENTRE;
    }

    protected function isOcUndertaking($condition)
    {
        return $condition['conditionType']['id'] === Condition::TYPE_UNDERTAKING
            && $condition['attachedTo']['id'] === Condition::ATTACHED_TO_OPERATING_CENTRE;
    }
}

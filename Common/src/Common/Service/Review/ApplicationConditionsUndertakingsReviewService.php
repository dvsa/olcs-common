<?php

/**
 * Application Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Review;

use Common\Service\Entity\ConditionUndertakingEntityService as Condition;

/**
 * Application Conditions Undertakings Review Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConditionsUndertakingsReviewService extends AbstractReviewService
{
    /**
     * Format the readonly config from the given data
     *
     * @param array $data
     * @return array
     */
    public function getConfigFromData(array $data = array())
    {
        list($licConds, $licUnds, $ocConds, $ocUnds) = $this->splitUpConditionsAndUndertakings($data);

        $subSections = [];

        if (!empty($licConds)) {

            $subSection = [
                'header' => 'Licence conditions',
                'mainItems' => [
                    [
                        'multiItems' => [
                            [
                                [
                                    'label' => '',
                                    'value' => 'foo'
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }

        return [
            'subSections' => $subSections
        ];
    }

    protected function splitUpConditionsAndUndertakings($data)
    {
        $licConds = $licUnds = $ocConds = $ocUnds = [];

        foreach ($data['conditionUndertakings'] as $condition) {
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
                    break;
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

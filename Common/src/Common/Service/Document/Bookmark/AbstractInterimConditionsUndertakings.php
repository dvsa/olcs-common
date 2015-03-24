<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Abstract Interim Conditions / Undertakings
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractInterimConditionsUndertakings extends DynamicBookmark
{
    const CONDITION_TYPE = null;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Application',
            'data' => [
                'id' => $data['application']
            ],
            'bundle' => [
                'children' => [
                    /**
                     * Get the conditions and undertakings against the app. These
                     * could be new ones or delta records against those in the licence
                     *
                     * We can't filter these at the query level because we might then
                     * miss a delta record which was for example fulfilled but now is not,
                     * or even worse one which was attached to the licence but is now attached
                     * to an OC instead
                     */
                    'conditionUndertakings' => [
                        'children' => [
                            'attachedTo',
                            'conditionType',
                            'licConditionVariation'
                        ]
                    ],
                    'licence' => [
                        'children' => [
                            /**
                             * We have the luxury of being able to filter the C&Us against the
                             * licence since if they're not in a relevant state we aren't interested
                             * and if they HAVE been updated via an app delta, we'll get that in the
                             * application's child bundle instead
                             */
                            'conditionUndertakings' => [
                                'criteria' => [
                                    'isDraft' => false,
                                    'isFulfilled' => false,
                                    'conditionType' => static::CONDITION_TYPE,
                                    'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                                ],
                                'children' => [
                                    'attachedTo',
                                    'conditionType',
                                    'licConditionVariation'
                                ]
                            ]
                        ]
                    ],
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        $licenceConditions = $this->getIndexedData($this->data['licence']['conditionUndertakings']);
        $applicationConditions = $this->getIndexedData($this->data['conditionUndertakings']);

        $conditions = [];
        foreach ($licenceConditions as $id => $condition) {
            if (isset($applicationConditions[$id])) {
                $condition = $applicationConditions[$id];
            }

            if (
                $condition['isFulfilled'] === 'N'
                && $condition['conditionType']['id'] === static::CONDITION_TYPE
                && $condition['attachedTo']['id'] === ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                && $condition['action'] !== 'D'
            ) {
                $conditions[] = $condition['notes'];
            }
        }

        return implode("\n\n", $conditions);
    }

    protected function getIndexedData($conditions)
    {
        $final = [];
        foreach ($conditions as $condition) {
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

<?php
namespace Common\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Base\DynamicBookmark;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Abstract Conditions / Undertakings
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractConditionsUndertakings extends DynamicBookmark
{
    const CONDITION_TYPE = null;

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Licence',
            'data' => [
                'id' => $data['licence']
            ],
            'bundle' => [
                'children' => [
                    'conditionUndertakings' => [
                        'criteria' => [
                            'isDraft' => false,
                            'isFulfilled' => false,
                            'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE,
                            'conditionType' => static::CONDITION_TYPE
                        ],
                        'children' => [
                            'attachedTo',
                            'conditionType'
                        ]
                    ]
                ]
            ]
        ];

        return $query;
    }

    public function render()
    {
        return implode(
            "\n\n",
            array_map(
                function ($v) {
                    return $v['notes'];
                },
                $this->data['conditionUndertakings']
            )
        );
    }
}

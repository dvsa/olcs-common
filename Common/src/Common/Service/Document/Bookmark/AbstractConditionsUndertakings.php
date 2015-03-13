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
        $rows = [];
        foreach ($this->data['conditionUndertakings'] as $row) {

            if ($row['attachedTo']['id'] === ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                && $row['conditionType']['id'] === static::CONDITION_TYPE
                && $row['isFulfilled'] === 'N'
                && $row['isDraft'] === 'N'
            ) {
                $rows[] = $row['notes'];
            }
        }
        return implode("\n\n", $rows);
    }
}

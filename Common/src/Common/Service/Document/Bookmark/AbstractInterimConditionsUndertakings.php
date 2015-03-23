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

    protected $childBundle = [
        'criteria' => [
            'isDraft' => false,
            'isFulfilled' => false,
            'attachedTo' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE,
        ],
        'children' => [
            'attachedTo',
            'conditionType'
        ]
    ];

    public function getQuery(array $data)
    {
        $query = [
            'service' => 'Application',
            'data' => [
                'id' => $data['application']
            ],
            'bundle' => [
                'children' => [
                    'licence' => [
                        'children' => [
                            'conditionUndertakings' => []
                        ]
                    ],
                    'conditionUndertakings' => []
                ]
            ]
        ];

        // @TODO the below is seriously ugly
        $this->childBundle['criteria']['conditionType'] = static::CONDITION_TYPE;

        $query['bundle']['children']['conditionUndertakings'] = $this->childBundle;
        $query['bundle']['children']['licence']['children']['conditionUndertakings'] = $this->childBundle;

        return $query;
    }

    public function render()
    {
        $licenceConditions = $this->data['licence']['conditionUndertakings'];
        $applicationConditions = $this->data['conditionUndertakings'];

        // @TODO iterate through the licence conditions and key them based
        // on their ID
        // then iterate through application conditions and override any rows
        // based on the keyed ID
        // then array_filter out where action === D
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

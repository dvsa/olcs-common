<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\Undertakings;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Undertakings test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class UndertakingsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new Undertakings();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new Undertakings();
        $bookmark->setData(
            [
                'conditionUndertakings' => [
                    [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntityService::TYPE_UNDERTAKING
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'a note'
                    ], [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntityService::TYPE_UNDERTAKING
                        ],
                        'isFulfilled' => 'Y',
                        'isDraft' => 'N',
                        'notes' => 'another note'
                    ], [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntityService::TYPE_UNDERTAKING
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'a third note'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            "a note\n\na third note",
            $bookmark->render()
        );
    }
}

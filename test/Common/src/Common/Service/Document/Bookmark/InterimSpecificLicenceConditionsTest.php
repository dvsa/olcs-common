<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\InterimSpecificLicenceConditions;
use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Interim Conditions test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimSpecificLicenceConditionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new InterimSpecificLicenceConditions();
        $query = $bookmark->getQuery(['application' => 123]);

        $this->assertEquals('Application', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new InterimSpecificLicenceConditions();
        $bookmark->setData(
            [
                'conditionUndertakings' => [
                    [
                        'id' => 10,
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntityService::TYPE_CONDITION
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'a new note',
                        'action' => 'A'
                    ], [
                        'id' => 30,
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntityService::TYPE_CONDITION
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'an updated note',
                        'action' => 'U',
                        'licConditionVariation' => [
                            'id' => 20
                        ]
                    ]
                ],
                'licence' => [
                    'conditionUndertakings' => [
                        [
                            'id' => 20,
                            'attachedTo' => [
                                'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                            ],
                            'conditionType' => [
                                'id' => ConditionUndertakingEntityService::TYPE_CONDITION
                            ],
                            'isFulfilled' => 'N',
                            'isDraft' => 'N',
                            'notes' => 'another note',
                            'action' => null
                        ],
                        [
                            'id' => 40,
                            'attachedTo' => [
                                'id' => ConditionUndertakingEntityService::ATTACHED_TO_LICENCE
                            ],
                            'conditionType' => [
                                'id' => ConditionUndertakingEntityService::TYPE_CONDITION
                            ],
                            'isFulfilled' => 'N',
                            'isDraft' => 'N',
                            'notes' => 'an original note',
                            'action' => null
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            "an updated note\n\nan original note\n\na new note",
            $bookmark->render()
        );
    }
}

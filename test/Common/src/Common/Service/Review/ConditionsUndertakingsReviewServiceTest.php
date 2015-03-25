<?php

/**
 * Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Review\ConditionsUndertakingsReviewService;
use Common\Service\Entity\ConditionUndertakingEntityService as Condition;

/**
 * Conditions Undertakings Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ConditionsUndertakingsReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ConditionsUndertakingsReviewService();
    }

    public function testGetConfigFromData()
    {
        $this->assertNull($this->sut->getConfigFromData([]));
    }

    public function testFormatLicencesSubSection()
    {
        // Params
        $list = [
            ['notes' => '123'],
            ['notes' => '456'],
            ['notes' => '789']
        ];
        $lva = 'application';
        $conditionOrUndertaking = 'conditions';
        $action = 'added';
        $expected = [
            'title' => 'application-review-conditions-undertakings-licence-conditions-added',
            'mainItems' => [
                [
                    'multiItems' => [
                        [
                            [
                                'list' => ['123', '456', '789']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->sut->formatLicenceSubSection($list, $lva, $conditionOrUndertaking, $action)
        );
    }

    public function testFormatOcSubSection()
    {
        // Params
        $list = [
            [
                [
                    'notes' => '123',
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => '123 street',
                            'town' => 'Footown'
                        ]
                    ]
                ],
                ['notes' => '456'],
                ['notes' => '789']
            ],
            [
                [
                    'notes' => '123',
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => '321 street',
                            'town' => 'Footown'
                        ]
                    ]
                ]
            ]
        ];
        $lva = 'application';
        $conditionOrUndertaking = 'conditions';
        $action = 'added';
        $expected = [
            'title' => 'application-review-conditions-undertakings-oc-conditions-added',
            'mainItems' => [
                [
                    'header' => '123 street, Footown',
                    'multiItems' => [
                        [
                            [
                                'list' => ['123', '456', '789']
                            ]
                        ]
                    ]
                ],
                [
                    'header' => '321 street, Footown',
                    'multiItems' => [
                        [
                            [
                                'list' => ['123']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->sut->formatOcSubSection($list, $lva, $conditionOrUndertaking, $action)
        );
    }

    public function testFormatConditionsList()
    {
        // Params
        $conditions = [
            ['notes' => 'abc'],
            ['notes' => 'def']
        ];
        $expected = ['abc', 'def'];

        $this->assertEquals($expected, $this->sut->formatConditionsList($conditions));
    }

    public function testSplitUpConditionsAndUndertakingsWithEmptyLists()
    {
        // Params
        $data = [
            'conditionUndertakings' => []
        ];
        $expectedLicConds = [];
        $expectedLicUnds = [];
        $expectedOcConds = [];
        $expectedOcUnds = [];

        list($licConds, $licUnds, $ocConds, $ocUnds) = $this->sut->splitUpConditionsAndUndertakings($data);

        $this->assertEquals($expectedLicConds, $licConds);
        $this->assertEquals($expectedLicUnds, $licUnds);
        $this->assertEquals($expectedOcConds, $ocConds);
        $this->assertEquals($expectedOcUnds, $ocUnds);
    }

    public function testSplitUpConditionsAndUndertakings()
    {
        // Params
        $data = [
            'conditionUndertakings' => [
                // Added licence conditions
                [
                    'action' => 'A',
                    'notes' => 'Added licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                // Updated licence conditions
                [
                    'action' => 'U',
                    'notes' => 'Updated licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                // Deleted licence conditions
                [
                    'action' => 'D',
                    'notes' => 'Deleted licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                // Added licence undertakings
                [
                    'action' => 'A',
                    'notes' => 'Added licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                // Updated licence undertakings
                [
                    'action' => 'U',
                    'notes' => 'Updated licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                // Deleted licence undertakings
                [
                    'action' => 'D',
                    'notes' => 'Deleted licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                // Added oc conditions
                [
                    'action' => 'A',
                    'notes' => 'Added oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 222]
                ],
                // Updated oc conditions
                [
                    'action' => 'U',
                    'notes' => 'Updated oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 222]
                ],
                // Deleted oc conditions
                [
                    'action' => 'D',
                    'notes' => 'Deleted oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted oc condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 222]
                ],
                // Added oc undertakings
                [
                    'action' => 'A',
                    'notes' => 'Added oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 222]
                ],
                // Updated oc undertakings
                [
                    'action' => 'U',
                    'notes' => 'Updated oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 222]
                ],
                // Deleted oc undertakings
                [
                    'action' => 'D',
                    'notes' => 'Deleted oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 111]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted oc undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                    'operatingCentre' => ['id' => 222]
                ],
            ]
        ];
        $expectedLicConds = [
            'A' => [
                [
                    'action' => 'A',
                    'notes' => 'Added licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ]
            ],
            'U' => [
                [
                    'action' => 'U',
                    'notes' => 'Updated licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ]
            ],
            'D' => [
                [
                    'action' => 'D',
                    'notes' => 'Deleted licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted licence condition',
                    'conditionType' => ['id' => Condition::TYPE_CONDITION],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ]
            ]
        ];
        $expectedLicUnds = [
            'A' => [
                [
                    'action' => 'A',
                    'notes' => 'Added licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'A',
                    'notes' => 'Another added licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ]
            ],
            'U' => [
                [
                    'action' => 'U',
                    'notes' => 'Updated licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'U',
                    'notes' => 'Another updated licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ]
            ],
            'D' => [
                [
                    'action' => 'D',
                    'notes' => 'Deleted licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ],
                [
                    'action' => 'D',
                    'notes' => 'Another deleted licence undertaking',
                    'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                    'attachedTo' => ['id' => Condition::ATTACHED_TO_LICENCE]
                ]
            ]
        ];
        $expectedOcConds = [
            'A' => [
                111 => [
                    [
                        'action' => 'A',
                        'notes' => 'Added oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                    [
                        'action' => 'A',
                        'notes' => 'Another added oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ]
                ],
                222 => [
                    [
                        'action' => 'A',
                        'notes' => 'Another added oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 222]
                    ],
                ]
            ],
            'U' => [
                111 => [
                    [
                        'action' => 'U',
                        'notes' => 'Updated oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                    [
                        'action' => 'U',
                        'notes' => 'Another updated oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                ],
                222 => [
                    [
                        'action' => 'U',
                        'notes' => 'Another updated oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 222]
                    ],
                ]
            ],
            'D' => [
                111 => [
                    [
                        'action' => 'D',
                        'notes' => 'Deleted oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                    [
                        'action' => 'D',
                        'notes' => 'Another deleted oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                ],
                222 => [
                    [
                        'action' => 'D',
                        'notes' => 'Another deleted oc condition',
                        'conditionType' => ['id' => Condition::TYPE_CONDITION],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 222]
                    ],
                ]
            ]
        ];
        $expectedOcUnds = [
            'A' => [
                111 => [
                    [
                        'action' => 'A',
                        'notes' => 'Added oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                    [
                        'action' => 'A',
                        'notes' => 'Another added oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ]
                ],
                222 => [
                    [
                        'action' => 'A',
                        'notes' => 'Another added oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 222]
                    ],
                ]
            ],
            'U' => [
                111 => [
                    [
                        'action' => 'U',
                        'notes' => 'Updated oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                    [
                        'action' => 'U',
                        'notes' => 'Another updated oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                ],
                222 => [
                    [
                        'action' => 'U',
                        'notes' => 'Another updated oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 222]
                    ],
                ]
            ],
            'D' => [
                111 => [
                    [
                        'action' => 'D',
                        'notes' => 'Deleted oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                    [
                        'action' => 'D',
                        'notes' => 'Another deleted oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 111]
                    ],
                ],
                222 => [
                    [
                        'action' => 'D',
                        'notes' => 'Another deleted oc undertaking',
                        'conditionType' => ['id' => Condition::TYPE_UNDERTAKING],
                        'attachedTo' => ['id' => Condition::ATTACHED_TO_OPERATING_CENTRE],
                        'operatingCentre' => ['id' => 222]
                    ],
                ]
            ]
        ];

        list($licConds, $licUnds, $ocConds, $ocUnds) = $this->sut->splitUpConditionsAndUndertakings($data);

        $this->assertEquals($expectedLicConds, $licConds);
        $this->assertEquals($expectedLicUnds, $licUnds);
        $this->assertEquals($expectedOcConds, $ocConds);
        $this->assertEquals($expectedOcUnds, $ocUnds);
    }
}

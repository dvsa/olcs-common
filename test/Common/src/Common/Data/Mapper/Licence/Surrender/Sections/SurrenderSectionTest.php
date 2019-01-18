<?php


namespace CommonTest\Data\Mapper\Licence\Surrender\Sections;

use Common\Data\Mapper\licence\Surrender\OperatorLicence;
use Common\Data\Mapper\Licence\Surrender\Sections\SurrenderSection;
use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Zend\Mvc\Controller\Plugin\Url;

class SurrenderSectionTest extends MockeryTestCase
{

    protected $mockTranslator;
    protected $mockUrlHelper;


    public function setUp()
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->mockUrlHelper = m::mock(Url::class);
    }

    /**
     * @dataProvider  surrenderDiscs
     */
    public function testDiscsSurrenderSection($data, $expected)
    {
        $mockSurrender = ['surrender' => $data];
        $this->mockTranslator->shouldReceive('translate')->with('DISCHEADING')->andReturn('DISCHEADING');
        $this->mockTranslator->shouldReceive('translate')->with('licence.surrender.review.label.discs.possession')->andReturn($expected[0]['label']);
        $this->mockTranslator->shouldReceive('translate')->with('licence.surrender.review.label.discs.lost')->andReturn($expected[1]['label']);
        $this->mockTranslator->shouldReceive('translate')->with('licence.surrender.review.label.discs.stolen')->andReturn($expected[2]['label']);
        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/current-discs/GET', [], [], true)->andReturn('discBackLInk');

        $sut = new SurrenderSection(
            $mockSurrender,
            $this->mockUrlHelper,
            $this->mockTranslator,
            SurrenderSection::DISC_SECTION
        );
        $sut->setHeading('DISCHEADING');
        $sut->setDisplayChangeLinkInHeading(true);
        $expected = [
            'sectionHeading' => 'DISCHEADING',
            'changeLinkInHeading' => true,
            'change'=> ['sectionLink' => 'discBackLInk'],
            'questions' => $expected
        ];
        $this->assertSame($expected, $sut->makeSection());
    }

    public function surrenderDiscs()
    {
        $changeLinkInHeading = true;
        return [

            'DestroyedDiscs' => [
                [
                    'discDestroyed' => '10',
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'Y',
                        'info' => [
                            'number' => '10'
                        ]
                    ],
                    'lostSection' => [
                        'lost' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null
                        ]
                    ],
                    'stolenSection' => [
                        'stolen' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null
                        ]
                    ]
                ],

                [
                    [
                        'label' => 'Number destroyed',
                        'answer' => '10',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number lost',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number stolen',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk' ]
                    ]
                ]

            ],
            'LostDiscs' => [
                [
                    'discLost' => '10',
                    'discLostInfo' => 'dog ate them',
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'N',
                        'info' => [
                            'number' => null
                        ]
                    ],
                    'lostSection' => [
                        'lost' => 'Y',
                        'info' => [
                            'number' => '10',
                            'details' => 'dog ate them'
                        ]
                    ],
                    'stolenSection' => [
                        'stolen' => 'N',
                        'info' => [
                            'number' => null,
                            'details' => null
                        ]
                    ]
                ],

                [
                    [
                        'label' => 'Number destroyed',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number lost',
                        'answer' => '10',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number stolen',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk' ]
                    ]
                ]

            ],
            'StolenDiscs' => [
                [
                    'discStolen' => '9',
                    'discLost' => null,
                    'discStolenInfo' => 'Crime ref #1 - it was a fair cop',
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'N',
                        'info' => [
                            'number' => null
                        ]
                    ],
                    'lostSection' => [
                        'lost' => 'Y',
                        'info' => [
                            'number' => '0',
                            'details' => 'dog ate them'
                        ]
                    ],
                    'stolenSection' => [
                        'stolen' => 'Y',
                        'info' => [
                            'number' => '9',
                            'details' => 'Crime ref #1 - it was a fair cop'
                        ]
                    ]
                ],

                [
                    [
                        'label' => 'Number destroyed',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number lost',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number stolen',
                        'answer' => '9',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk' ]
                    ]
                ]

            ],
            'AllTypesDiscs' => [
                [
                    'discStolen' => '10',
                    'discLost' => '10',
                    'discLostInfo' => 'dog ate them',
                    'discStolenInfo' => 'Crime ref #1 - it was a fair cop',
                    'discDestroyed' => '10',
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'N',
                        'info' => [
                            'number' => '10'
                        ]
                    ],
                    'lostSection' => [
                        'lost' => 'Y',
                        'info' => [
                            'number' => '10',
                            'details' => 'dog ate them'
                        ]
                    ],
                    'stolenSection' => [
                        'stolen' => 'Y',
                        'info' => [
                            'number' => '10',
                            'details' => 'Crime ref #1 - it was a fair cop'
                        ]
                    ]
                ],

                [
                    [
                        'label' => 'Number destroyed',
                        'answer' => '10',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number lost',
                        'answer' => '10',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk']
                    ],
                    [
                        'label' => 'Number stolen',
                        'answer' => '10',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'discBackLInk' ]
                    ]
                ]

            ]
        ];
    }

    /**
     * @dataProvider documentSections
     */
    public function testDocumentsSectionInternationalLicence($data, $expected)
    {
        $mockSurrender = ['surrender' => $data];
        $this->mockTranslator->shouldReceive('translate')->with('DOCUMENTHEADING')->andReturn('DOCUMENTHEADING');
        $this->mockTranslator->shouldReceive('translate')->with('surrender.review.label.documents.operatorLicenceDocument')->andReturn($expected[0]['label']);
        $this->mockTranslator->shouldReceive('translate')->with('surrender.review.label.documents.communityLicenceDocument')->andReturn($expected[1]['label']);

        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/operator-licence/GET', [], [], true);

        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/community-licence/GET', [], [], true);


        $sut = new SurrenderSection(
            $mockSurrender,
            $this->mockUrlHelper,
            $this->mockTranslator,
            SurrenderSection::OPERATORLICENCE_SECTION
        );

        $sut->setHeading('DOCUMENTHEADING');
        $sut->setDisplayChangeLinkInHeading(true);
        $expected = [
            'sectionHeading' => 'DOCUMENTHEADING',
            'changeLinkInHeading' => false,
            'change'=> ['sectionLink' => null],
            'questions' => $expected
        ];
        $this->assertSame($expected, $sut->makeSection());
    }

    public function documentSections()
    {
        $changeLinkInHeading = false;
        return [

            'OperatorLicenceAndCommunityLicence' => [
                [

                    'licenceDocumentStatus' =>
                        [
                            'id' => RefData::SURRENDER_DOC_STATUS_DESTROYED,
                        ],
                    'licenceDocumentInfo' => null

                ],

                [
                    [
                        'label' => 'Operator licence',
                        'answer' => 'destroyed',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'operator-licence']
                    ],
                    [
                        'label' => 'Community licence and all certified copies',
                        'answer' => 'destroyed',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'community-licence']
                    ]
                ]

            ],

        ];
    }
}

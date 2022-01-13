<?php

namespace CommonTest\Data\Mapper\Licence\Surrender\Sections;

use Common\Data\Mapper\licence\Surrender\OperatorLicence;
use Common\Data\Mapper\Licence\Surrender\Sections\SurrenderSection;
use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Mvc\Controller\Plugin\Url;

class SurrenderSectionTest extends MockeryTestCase
{
    protected $mockTranslator;
    protected $mockUrlHelper;


    public function setUp(): void
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
        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/current-discs/review/GET', [], [], true)->andReturn('discBackLInk');

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
     * @dataProvider operatorLicenceSection
     */
    public function testOperatorLicence($data, $expected)
    {
        $mockSurrender = ['surrender' => $data];
        $this->mockTranslator->shouldReceive('translate')->with('DOCUMENTHEADING')->andReturn('DOCUMENTHEADING');
        $this->mockTranslator->shouldReceive('translate')->with('surrender.review.label.documents.operatorLicenceDocument')->andReturn($expected[0]['label']);

        // use this to get the appropriate translation key
        $translationString = $this->getTranslationStrings($this->dataName());

        $this->mockTranslator->shouldReceive('translate')->with($translationString)->andReturn($expected[0]['answer']);


        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/operator-licence/review/GET', [], [], true)->andReturn($expected[0]['change']['sectionLink']);


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
            'changeLinkInHeading' => true,
            'change'=> ['sectionLink' => $expected[0]['change']['sectionLink']],
            'questions' => $expected
        ];
        $this->assertSame($expected, $sut->makeSection());
    }

    public function operatorLicenceSection()
    {
        $changeLinkInHeading = true;
        return [

            'OperatorLicenceDestroyed' => [
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
                        'answer' => 'to be destroyed',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'operator-licence']
                    ]
                ]

            ],
            'OperatorLicenceLost' => [
                [

                    'licenceDocumentStatus' =>
                        [
                            'id' => RefData::SURRENDER_DOC_STATUS_LOST,
                        ],
                    'licenceDocumentInfo' => 'lost content'

                ],

                [
                    [
                        'label' => 'Operator licence',
                        'answer' => 'lost',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'operator-licence']
                    ]
                ]

            ],
            'OperatorLicenceStolen' => [
                [

                    'licenceDocumentStatus' =>
                        [
                            'id' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                        ],
                    'licenceDocumentInfo' => 'stolen content'

                ],

                [
                    [
                        'label' => 'Operator licence',
                        'answer' => 'stolen',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'operator-licence']
                    ]
                ]

            ],

        ];
    }

    private function getTranslationStrings(string $dataDescription):string
    {
        $translations = [
            'OperatorLicenceLost'=>'licence.surrender.review.label.documents.answerlost',
            'OperatorLicenceDestroyed'=>'licence.surrender.review.label.documents.answerpossession',
            'OperatorLicenceStolen' =>'licence.surrender.review.label.documents.answerstolen',
            'communityLicenceDestroyed' =>'licence.surrender.review.label.documents.answerpossession',
            'communityLicenceStolen' =>'licence.surrender.review.label.documents.answerstolen',
            'communityLicenceLost' =>'licence.surrender.review.label.documents.answerlost',

        ];
        return $translations[$dataDescription];
    }

    /**
     * @dataProvider communityLicenceSection
     */
    public function testCommunityLicence($data, $expected)
    {
        $mockSurrender = ['surrender' => $data];
        $this->mockTranslator->shouldReceive('translate')->with('DOCUMENTHEADING')->andReturn('DOCUMENTHEADING');
        $this->mockTranslator->shouldReceive('translate')->with('surrender.review.label.documents.communityLicenceDocument')->andReturn($expected[0]['label']);

        // use this to get the appropriate translation key
        $translationString = $this->getTranslationStrings($this->dataName());

        $this->mockTranslator->shouldReceive('translate')->with($translationString)->andReturn($expected[0]['answer']);


        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/community-licence/review/GET', [], [], true)->andReturn($expected[0]['change']['sectionLink']);


        $sut = new SurrenderSection(
            $mockSurrender,
            $this->mockUrlHelper,
            $this->mockTranslator,
            SurrenderSection::COMMUNITYLICENCE_SECTION
        );

        $sut->setHeading('DOCUMENTHEADING');
        $sut->setDisplayChangeLinkInHeading(true);
        $expected = [
            'sectionHeading' => 'DOCUMENTHEADING',
            'changeLinkInHeading' => true,
            'change'=> ['sectionLink' => $expected[0]['change']['sectionLink']],
            'questions' => $expected
        ];
        $this->assertSame($expected, $sut->makeSection());
    }

    public function communityLicenceSection()
    {
        $changeLinkInHeading = true;
        return [

            'communityLicenceDestroyed' => [
                [

                    "communityLicenceDocumentStatus" => ['id' => 'doc_sts_destroyed'],
                    "communityLicenceDocumentInfo" => null,

                ],

                [
                    [
                        'label' => 'Community licence',
                        'answer' => 'to be destroyed',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'community-licence']
                    ]
                ]

            ],
            'communityLicenceLost' => [
                [

                    'communityLicenceDocumentStatus' =>
                        [
                            'id' => RefData::SURRENDER_DOC_STATUS_LOST,
                        ],
                    'communityLicenceDocumentInfo' => 'lost content'

                ],

                [
                    [
                        'label' => 'Community licence',
                        'answer' => 'lost',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'community-licence']
                    ]
                ]

            ],
            'communityLicenceStolen' => [
                [

                    'communityLicenceDocumentStatus' =>
                        [
                            'id' => RefData::SURRENDER_DOC_STATUS_STOLEN,
                        ],
                    'communityLicenceDocumentInfo' => 'stolen content'

                ],

                [
                    [
                        'label' => 'Community licence',
                        'answer' => 'stolen',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => 'community-licence']
                    ]
                ]

            ],

        ];
    }
}

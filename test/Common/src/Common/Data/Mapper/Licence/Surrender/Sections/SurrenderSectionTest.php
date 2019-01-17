<?php


namespace CommonTest\Data\Mapper\Licence\Surrender\Sections;


use Common\Data\Mapper\Licence\Surrender\Sections\SurrenderSection;
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
        $this->mockUrlHelper->shouldReceive('fromRoute')->with('licence/surrender/current-discs/GET', [], [], true);

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
            'change'=> ['sectionLink' => null],
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
                        'change' => ['sectionLink' => null]
                    ],
                    [
                        'label' => 'Number lost',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => null]
                    ],
                    [
                        'label' => 'Number stolen',
                        'answer' => '0',
                        'changeLinkInHeading' => $changeLinkInHeading,
                        'change' => ['sectionLink' => null ]
                    ]
                ]

            ]
        ];
    }
}
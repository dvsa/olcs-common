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
        $this->mockTranslator->shouldReceive('translate')->with('DISCHEADING')->andReturn('DISCHEADING');
        $this->mockUrlHelper = m::mock(Url::class);
    }

    /**
     * @dataProvider  surrenderDiscs
     */
    public function testDiscsSurrenderSection($data, $expected)
    {
        $mockSurrender = $data;

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
            'change' => false,
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
                    'version' => 1,
                    'possessionSection' => [
                        'inPossession' => 'Y',
                        'info' => [
                            'number' => 10
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
                        'answer' => 10,
                        'changeLinkInHeading' => $changeLinkInHeading
                    ],
                    [
                        'label' => 'Number lost',
                        'answer' => 0,
                        'changeLinkInHeading' => $changeLinkInHeading
                    ],
                    [
                        'label' => 'Number stolen',
                        'answer' => 0,
                        'changeLinkInHeading' => $changeLinkInHeading
                    ]
                ]

            ]
        ];
    }
}
<?php


namespace CommonTest\Data\Mapper\Lva\TransportManager\Sections;

use Common\Data\Mapper\Lva\TransportManager\Sections\OtherEmployment;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class OtherEmploymentsTest extends MockeryTestCase
{

    private $mockTranslator;
    private $sut;


    public function setUp()
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->sut = new OtherEmployment($this->mockTranslator);
    }

    public function testObjectPopulated()
    {
        $actual = $this->sut->populate(
            [
                'transportManager' =>[
                    'employments'=>[]
                ]
            ]
        );

        $this->assertInstanceOf(OtherEmployment::class, $actual);
        $this->assertEquals(['lva-tmverify-details-checkanswer-employments' => 'None Added'], $actual->sectionSerialize());
    }

    public function testObjectPopulatedWithEmployment()
    {
        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-otherEmployments', ['__TEST__'])->once()->andReturn('__TEST__');

        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-otherEmployments', ['__TEST__'])->once()->andReturn('__TEST__');

        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer--otherEmployments-more', [1])->once()->andReturn('__TEST__');

        $actual = $this->sut->populate(
            [
                'transportManager' =>[
                    'employments'=>[
                        0 =>[
                            'employerName' =>'__TEST__'
                        ],
                        1 =>[
                            'employerName' =>'__TEST__'
                        ],
                        2 =>[
                            'employerName' =>'__TEST__'
                        ],
                        3 =>[
                            'employerName' =>'__TEST__'
                        ]
                    ]
                ]
            ]
        );

        $this->assertInstanceOf(OtherEmployment::class, $actual);
        $this->assertEquals(['lva-tmverify-details-checkanswer-employments' => '__TEST__'], $actual->sectionSerialize());
    }

}

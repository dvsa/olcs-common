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
        foreach (get_object_vars($this->sut) as $property) {
            $this->assertNotEmpty($property);
        }
    }
}

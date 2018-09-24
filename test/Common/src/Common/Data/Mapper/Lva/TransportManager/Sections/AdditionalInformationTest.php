<?php


namespace CommonTest\Data\Mapper\Lva\TransportManager\Sections;

use Common\Data\Mapper\Lva\TransportManager\Sections\AdditionalInformation;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class AdditionalInformationTest extends MockeryTestCase
{
    private $mockTranslator;
    private $sut;

    public function setUp()
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->sut = new AdditionalInformation($this->mockTranslator);
    }

    public function testObjectPopulated()
    {
        $actual = $this->sut->populate([
            'transportManager' =>[
                'documents' => []
            ],
            'additionalInformation'=>'__TEST__',

            ]);
        $this->assertInstanceOf(AdditionalInformation::class, $actual);
        foreach (get_object_vars($this->sut) as $property) {
            $this->assertNotEmpty($property);
        }
    }
}

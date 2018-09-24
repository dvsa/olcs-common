<?php


namespace CommonTest\Data\Mapper\Lva\TransportManager\Sections;

use Common\Data\Mapper\Lva\TransportManager\Sections\RevokedLicences;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class RevokedLicencesTest extends MockeryTestCase
{
    private $mockTranslator;
    private $sut;


    public function setUp()
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->sut = new RevokedLicences($this->mockTranslator);
    }

    public function testPopulatedObject()
    {
        $actual = $this->sut->populate(
            [
                'transportManager' =>[
                    'otherLicences' => [
                    ],
                ]
            ]
        );
        $this->assertInstanceOf(RevokedLicences::class, $actual);

        $this->assertEquals(['lva-tmverify-details-checkanswer-revokedLicences' => 'None Added'], $actual->sectionSerialize());
    }
}

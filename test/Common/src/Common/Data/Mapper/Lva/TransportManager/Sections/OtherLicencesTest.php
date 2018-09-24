<?php


namespace CommonTest\Data\Mapper\Lva\TransportManager\Sections;

use Common\Data\Mapper\Lva\TransportManager\Sections\OtherLicences;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class OtherLicencesTest extends MockeryTestCase
{
    private $mockTranslator;
    private $sut;

    public function setUp()
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->sut = new OtherLicences($this->mockTranslator);
    }

    public function testObjectPopulates()
    {
        $this->mockTranslator->shouldReceive(
            'translateReplace'
        )->with('markup-lva-tmverify-details-checkanswer-answer-otherLicences', ['OB123'])->once()->andReturn('__TEST__');

        $actual = $this->sut->populate(
            [
                'otherLicences' => [
                    ['licNo' => 'OB123']
                ],

            ]
        );
        $this->assertInstanceOf(OtherLicences::class, $actual);

        $this->assertEquals(['lva-tmverify-details-checkanswer-licences' => '__TEST__'], $actual->sectionSerialize());
    }

    public function testObjectPopulatesNoneAdded()
    {
        $actual = $this->sut->populate(
            [
                'otherLicences' => [
                ],

            ]
        );
        $this->assertInstanceOf(OtherLicences::class, $actual);

        $this->assertEquals(['lva-tmverify-details-checkanswer-licences' => 'None Added'], $actual->sectionSerialize());
    }
}

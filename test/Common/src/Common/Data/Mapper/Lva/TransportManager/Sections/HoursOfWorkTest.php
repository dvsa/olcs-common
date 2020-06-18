<?php


namespace CommonTest\Data\Mapper\Lva\TransportManager\Sections;

use Common\Data\Mapper\Lva\TransportManager\Sections\HoursOfWork;
use Common\Service\Helper\TranslationHelperService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

class HoursOfWorkTest extends MockeryTestCase
{

    private $sut;
    private $mockTranslator;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->sut = new HoursOfWork($this->mockTranslator);
    }

    public function testPopulateObject()
    {

        $actual = $this->sut->populate(
            [
                'hoursMon' => '__TEST__',
                'hoursTue' => '__TEST__',
                'hoursWed' => '__TEST__',
                'hoursThu' => '__TEST__',
                'hoursFri' => '__TEST__',
                'hoursSat' => '__TEST__',
                'hoursSun' => '__TEST__',
            ]
        );
        $this->assertInstanceOf(HoursOfWork::class, $actual);
        $this->assertNotEmpty($actual->sectionSerialize());
    }
}

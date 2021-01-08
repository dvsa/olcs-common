<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;

/**
 * @covers \Common\Data\Object\Search\BusRegSelfserve
 */
class BusRegSelfserveTest extends SearchAbstractTest
{
    protected $class = \Common\Data\Object\Search\BusRegSelfserve::class;

    public function testRegNoFormatter()
    {
        $data = [
            'busregId' => 'unit_BusRegId',
            'busRegStatus' => 'unit_Status',
            'regNo' => 'unit_RegNo',
        ];

        $expectUrl = 'EXPECT_URL';

        $mockUrl = m::mock()
            ->shouldReceive('fromRoute')
            ->once()
            ->with('search-bus/details', ['busRegId' => 'unit_BusRegId'])
            ->andReturn($expectUrl)
            ->getMock();

        $mockTranslator = m::mock(\Laminas\I18n\Translator\Translator::class)
            ->shouldReceive('translate')
            ->once()
            ->andReturnUsing(
                function ($key) {
                    return '_TRNSL_' . $key;
                }
            )
            ->getMock();

        $mockSm = m::mock(\Laminas\Di\ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('Helper\Url')->andReturn($mockUrl)
            ->shouldReceive('get')->with('translator')->andReturn($mockTranslator)
            ->getMock();

        $colRegNo = $this->sut->getColumns()[0];

        static::assertEquals(
            '<a href="'.$expectUrl.'">unit_RegNo</a><br/>_TRNSL_unit_Status',
            $colRegNo['formatter']($data, null, $mockSm)
        );
    }
}

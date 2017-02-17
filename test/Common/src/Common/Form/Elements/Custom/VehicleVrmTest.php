<?php

namespace CommonTest\Form\Elements\Custom;

use Common\Form\Elements\Custom\VehicleVrm;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Form\Elements\Custom\VehicleVrm
 */
class VehicleVrmTest extends MockeryTestCase
{
    public function testValidators()
    {
        /** @var VehicleVrm $sut */
        $sut = m::mock(VehicleVrm::class)->makePartial()
            ->shouldReceive('getName')->once()->andReturn('unit_Name')
            ->getMock();

        $actual = $sut->getInputSpecification();

        static::assertEquals('unit_Name', $actual['name']);
        static::assertTrue($actual['required']);
        static::assertInstanceOf(\Common\Filter\Vrm::class, current($actual['filters']));
        static::assertInstanceOf(\Dvsa\Olcs\Transfer\Validators\Vrm::class, current($actual['validators']));
    }
}

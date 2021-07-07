<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\OcNoOfLgvVehiclesRequired;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class OcNoOfLgvVehiclesRequiredTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcNoOfLgvVehiclesRequiredTest extends TestCase
{
    /**
     * @dataProvider dpFormatDataProvider
     */
    public function testFormat($noOfLgvVehiclesRequired, $expected)
    {
        $this->assertEquals(
            $expected,
            OcNoOfLgvVehiclesRequired::format(['noOfLgvVehiclesRequired' => $noOfLgvVehiclesRequired])
        );
    }

    public function dpFormatDataProvider()
    {
        return [
            [5, 5],
            [0, 0],
            [null, 0],
        ];
    }
}

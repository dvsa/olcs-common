<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\OcComplaints;

use Mockery as m;

/**
 * Class OcComplaintsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcComplaintsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFormatDataProvider
     */
    public function testFormat($data, $complaints)
    {
        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Entity\OcComplaints')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getCountComplaintsForOpCentre')
                    ->with(1)
                    ->andReturn($complaints)
                    ->getMock()
            )->getMock();

        $this->assertEquals(OcComplaints::format($data, array(), $sm), $complaints);
    }

    public function testFormatDataProvider()
    {
        return array(
            array(
                array(
                    'operatingCentre' => array(
                        'id' => 1
                    )
                ),
                4
            )
        );
    }
}

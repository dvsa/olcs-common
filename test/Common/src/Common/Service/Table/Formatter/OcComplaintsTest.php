<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\OcComplaints;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class OcComplaintsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcComplaintsTest extends TestCase
{
    /**
     * @dataProvider dpFormatDataProvider
     */
    public function testFormat($data, $complaints)
    {
        $this->assertEquals(OcComplaints::format($data), $complaints);
    }

    public function dpFormatDataProvider()
    {
        return array(
            array(
                array(
                    'operatingCentre' => array(
                        'complaints' => array(
                            array('id' => 1),
                            array('id' => 2),
                            array('id' => 3),
                        )
                    )
                ),
                3
            ),
            array(
                array(
                    'operatingCentre'
                ),
                0
            )
        );
    }
}

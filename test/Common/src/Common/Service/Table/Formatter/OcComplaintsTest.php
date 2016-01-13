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
        $this->assertEquals(OcComplaints::format($data), $complaints);
    }

    public function testFormatDataProvider()
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

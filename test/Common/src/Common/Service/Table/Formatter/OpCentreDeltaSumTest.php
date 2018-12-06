<?php

/**
 * OpCentreDeltaSumTest.php
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\OpCentreDeltaSum;
use Common\Service\Table\Formatter\Sum;

/**
 * Class SumTest
 *
 * OpCentreDelta sum test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OpCentreDeltaSumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFormatDataProvider
     */
    public function testFormat($data, $expected)
    {
        $column = array(
            'name' => 'colName'
        );

        $this->assertEquals(OpCentreDeltaSum::format($data, $column), $expected);
    }

    public function testFormatDataProvider()
    {
        return array(
            array(
                array(
                    array('action' => 'U', 'colName' => 1),
                    array('action' => 'E', 'colName' => 3),
                    array('action' => 'A', 'colName' => 4),
                    array('action' => 'C', 'colName' => 100),
                    array('action' => 'D', 'colName' => 100)
                ),
                8
            ),
            array(
                array(
                    array('action' => 'C', 'colName' => 100),
                    array('action' => 'D', 'colName' => 100)
                ),
                0
            ),
            array(
                array(
                    array('action' => 'E', 'colName' => 3),
                    array('action' => 'A', 'colName' => 4),
                ),
                7
            ),
            array(
                array(
                    array('action' => 'A', 'colName' => 4),
                ),
                4
            )
        );
    }
}

<?php

/**
 * Task date formatter tests
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TaskDate;

/**
 * Task date formatter tests
 *
 * @author Nick payne <nick.payne@valtech.co.uk>
 */
class TaskDateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $mockTranslator = $this->getMock('\stdClass', array('translate'));

        $sm = $this->getMock('\stdClass', array('get'));
        $sm->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));
        $this->assertEquals($expected, TaskDate::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(array('date' => '2013-01-01'), array('dateformat' => 'd/m/Y', 'name' => 'date'), '01/01/2013'),
            array(
                array('date' => '2013-01-01', 'urgent' => 'Y'),
                array('dateformat' => 'd/m/Y', 'name' => 'date'),
                '01/01/2013 (urgent)'
            )
        );
    }
}

<?php

/**
 * YesNo formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\YesNo;

/**
 * YesNo formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class YesNoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     * @group YesNoFormatter
     *
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
        $this->assertEquals($expected, YesNo::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(array('yesorno' => 1), array('name' => 'yesorno'), 'Yes'),
            array(array('yesorno' => 0), array('name' => 'yesorno'), 'No')
        );
    }
}

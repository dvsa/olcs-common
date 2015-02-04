<?php

/**
 * Translate formatter test
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\Translate;

/**
 * Translate formatter test
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     * @group TranslateFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $mockTranslator = $this->getMock('\stdClass', array('translate'));

        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will(
                $this->returnCallback(
                    function ($string) {
                        return strtoupper($string);
                    }
                )
            );

        $sm = $this->getMock('\stdClass', array('get'));
        $sm->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));

        $this->assertEquals($expected, Translate::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(array('test' => 'foo'), array('name' => 'test'), 'FOO'),
            array(array('test' => 'foo'), array('content' => 'test'), 'TEST'),
            array(array('test' => 'foo'), array(), ''),
            array(array('test' => ['foo' => 'bar']), array('name' => 'test->foo'), 'BAR')
        );
    }
}

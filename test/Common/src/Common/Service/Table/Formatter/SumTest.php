<?php

/**
 * Sum formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\Sum;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Sum formatter test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SumTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group SumFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $mockTranslator = $this->createPartialMock(Translator::class, array('translate'));

        $sm = $this->createMock(ServiceLocatorInterface::class);
        $sm->expects($this->any())
            ->method('get')
            ->with('translator')
            ->will($this->returnValue($mockTranslator));
        $this->assertSame($expected, Sum::format($data, $column, $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(array(), array(), '0'),
            array(array(), array('name' => 'subTotal'), '0'),
            array(array(array('subTotal' => 'A'), array('subTotal' => 'B')), array('name' => 'subTotal'), '0'),
            array(array(array('subTotal' => 5)), array('name' => 'subTotal'), '5'),
            array(array(array('subTotal' => 5), array('subTotal' => 7)), array('name' => 'subTotal'), '12'),
            array(
                array(
                    array('subTotal' => 5),
                    array('subTotal' => 7),
                    array('subTotal' => 'A')
                ),
                array('name' => 'subTotal'),
                '12'
            ),
            array(
                array(
                    array('subTotal' => 5),
                    array('subTotal' => 7),
                    array('subTotal' => 95)
                ),
                array('name' => 'subTotal'),
                '107'
            ),
            array(
                array(
                    array('subTotal' => '5.50'),
                    array('subTotal' => '7'),
                    array('subTotal' => '95.21')
                ),
                array('name' => 'subTotal'),
                '107.71'
            ),
        );
    }
}

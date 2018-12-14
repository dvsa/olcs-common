<?php

/**
 * Fee Amount Sum formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\FeeAmountSum;

/**
 * Fee Amount Sum formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeAmountSumTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $this->assertSame($expected, FeeAmountSum::format($data, $column));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(array(), array(), null),
            array(array(), array('name' => 'subTotal'), '£0.00'),
            array(array(array('subTotal' => 'A'), array('subTotal' => 'B')), array('name' => 'subTotal'), '£0.00'),
            array(array(array('subTotal' => 5)), array('name' => 'subTotal'), '£5.00'),
            array(array(array('subTotal' => 5), array('subTotal' => 7)), array('name' => 'subTotal'), '£12.00'),
            array(
                array(
                    array('subTotal' => 5),
                    array('subTotal' => 7),
                    array('subTotal' => 'A')
                ),
                array('name' => 'subTotal'),
                '£12.00'
            ),
            array(
                array(
                    array('subTotal' => 5),
                    array('subTotal' => 7),
                    array('subTotal' => 95)
                ),
                array('name' => 'subTotal'),
                '£107.00'
            ),
            array(
                array(
                    array('subTotal' => '5.11'),
                    array('subTotal' => 7),
                    array('subTotal' => '95.341')
                ),
                array('name' => 'subTotal'),
                '£107.45'
            ),
        );
    }
}

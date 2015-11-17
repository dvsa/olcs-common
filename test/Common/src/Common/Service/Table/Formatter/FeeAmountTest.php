<?php

/**
 * Fee Amount formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\FeeAmount;
use PHPUnit_Framework_TestCase;

/**
 * Fee Amount formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeAmountTest extends PHPUnit_Framework_TestCase
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
        $this->assertSame($expected, FeeAmount::format($data, $column));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            [[],[], ''],
            [['amount' => 25],['name' => 'amount'], '£25.00'],
            [['amount' => 251.40],['name' => 'amount'], '£251.40'],
            [
                [
                    'amount' => 120,
                    'vatAmount' => 20,
                ],
                ['name' => 'amount'],
                '£120.00<span class="status orange">includes VAT</span>'
            ],
        ];
    }
}

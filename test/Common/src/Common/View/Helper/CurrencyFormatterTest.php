<?php

namespace CommonTest\View\Helper;

use PHPUnit_Framework_TestCase;
use \Common\View\Helper\CurrencyFormatter;

/**
 * Test CurrencyFormatter view helper
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CurrencyFormatterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->viewHelper = new CurrencyFormatter();
    }

    /**
     * Test invoke
     * @dataProvider currencyDataProvider
     */
    public function testInvokeDefaultFields($input, $expected)
    {
        $this->assertEquals($expected, $this->viewHelper->__invoke($input['value']));
    }

    public function currencyDataProvider()
    {
        return [
            [
                // Full length fee ending in '00'
                ['value' => '10.00'],
                '£10'
            ],
            [
                // Single digit fee
                ['value' => '1'],
                '£1'
            ],
            [
                // Full length fee ending in non-'00'
                ['value' => '10.56'],
                '£10.56'
            ]
        ];
    }
}

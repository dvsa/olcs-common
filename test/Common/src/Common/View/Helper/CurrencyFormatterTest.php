<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\CurrencyFormatter;

/**
 * Test CurrencyFormatter view helper
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CurrencyFormatterTest extends \PHPUnit\Framework\TestCase
{
    public $viewHelper;
    /**
     * Setup the view helper
     */
    protected function setUp(): void
    {
        $this->viewHelper = new CurrencyFormatter();
    }

    /**
     * Test invoke
     * @dataProvider currencyDataProvider
     */
    public function testInvokeDefaultFields($value, $expected): void
    {
        $viewHelper = $this->viewHelper;
        $this->assertEquals($expected, $viewHelper($value));
    }

    public function currencyDataProvider()
    {
        return [
            ['10.00', '£10'],                // Full length fee ending in '00'
            ['1', '£1'],                     // Single digit fee
            ['10.56', '£10.56'],             // Full length fee ending in non-'00'
            ['3000', '£3,000'],              // Thousands without pence
            ['3000.00', '£3,000'],           // Thousands with explicit zero pence
            ['3000.56', '£3,000.56'],        // Thousands with non-zero pence
            ['24567', '£24,567'],            // Tens of thousands without pence
            ['24567.00', '£24,567'],         // Tens of thousands with explicit zero pence
            ['24567.22', '£24,567.22'],      // Tens of thousands with non-zero pence
            ['ABCXYZ', '£ABC,XYZ'],          // Unexpected input format
            ['ABC', '£ABC'],                 // Unexpected input format
            ['ABC.DEF.HIJ', '£ABC.DEF.HIJ'], // Unexpected input format
        ];
    }
}

<?php

/**
 * Financial History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\FinancialHistory;

/**
 * Financial History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FinancialHistoryTest extends \PHPUnit\Framework\TestCase
{
    public function testMapFromResult()
    {
        $input = [
            'foo' => 'bar',
            'insolvencyConfirmation' => 'FOO',
        ];

        $output = FinancialHistory::mapFromResult($input);

        $expected = [
            'data' => [
                'foo' => 'bar',
                'insolvencyConfirmation' => 'FOO',
                'financialHistoryConfirmation' => [
                    'insolvencyConfirmation' => 'FOO'
                ]
            ]
        ];

        $this->assertEquals($expected, $output);
    }
}

<?php
/**
 * Irhp Permit Stock Validity formatter test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IrhpPermitStockValidity;

class IrhpPermitStockValidityTest extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', 'd/m/Y');
        }
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group DateFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $this->assertEquals($expected, IrhpPermitStockValidity::format($data, $column));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'Normal Dates' => [
                [
                    'validFrom' => '2018-01-01T00:00:00+0000',
                    'validTo' => '2019-12-31T23:59:59+0000',
                ],
                [
                    'name' => 'validFrom',
                ],
                '01/01/2018 to 31/12/2019',
            ],
            'Null From' => [
                [
                    'validFrom' => null,
                    'validTo' => '2019-12-31T23:59:59+0000',
                ],
                [
                    'name' => 'validFrom',
                ],
                'N/A',
            ],
            'Null To' => [
                [
                    'validFrom' => '2018-01-01T00:00:00+0000',
                    'validTo' => null,
                ],
                [
                    'name' => 'validFrom',
                ],
                'N/A',
            ],
            'Both Null' => [
                [
                    'validFrom' => null,
                    'validTo' => null,
                ],
                [
                    'name' => 'validFrom',
                ],
                'N/A',
            ],
        ];
    }
}

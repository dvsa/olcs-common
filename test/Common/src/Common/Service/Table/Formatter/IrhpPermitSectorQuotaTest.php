<?php

/**
 * Irhp Permit Sector Quota Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\IrhpPermitSectorQuota;
use PHPUnit_Framework_TestCase;

class IrhpPermitSectorQuotaTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group IrhpPermitSectorFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, IrhpPermitSectorQuota::format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'Empty Quota Number' => [
                [
                    'quotaNumber' => '',
                    'sector' => [
                        'id' => '1',
                    ],
                ],
                "<input type='number' value='0' name='sectors[1]' />"
            ],
            'Non-Empty Quota Number' => [
                [
                    'quotaNumber' => '100',
                    'sector' => [
                        'id' => '1',
                    ],
                ],
                "<input type='number' value='100' name='sectors[1]' />"
            ],
        ];
    }
}

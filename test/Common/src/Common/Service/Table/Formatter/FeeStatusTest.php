<?php

/**
 * Fee status formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\FeeStatus;
use PHPUnit_Framework_TestCase;

/**
 * Fee status formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeStatusTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test the format method
     *
     * @group Formatters
     * @group FeeStatusFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, FeeStatus::format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'outstanding' => [
                [
                    'feeStatus' => [
                        'id' => 'lfs_ot',
                        'description' => 'outstanding'
                    ],
                ],
                '<span class="status orange">outstanding</span>',
            ],
            'paid' => [
                [
                    'feeStatus' => [
                        'id' => 'lfs_pd',
                        'description' => 'paid'
                    ],
                ],
                '<span class="status green">paid</span>',
            ],
            'cancelled' => [
                [
                    'feeStatus' => [
                        'id' => 'lfs_cn',
                        'description' => 'cancelled'
                    ],
                ],
                '<span class="status red">cancelled</span>',
            ],
            'other' => [
                [
                    'feeStatus' => [
                        'id' => 'foo',
                        'description' => 'foo'
                    ],
                ],
                '<span class="status grey">foo</span>',
            ],
        ];
    }
}

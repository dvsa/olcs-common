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
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_ot',
                        'description' => 'outstanding'
                    ],
                ],
                '99 <span class="status red">outstanding</span>',
            ],
            'paid' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_pd',
                        'description' => 'paid'
                    ],
                ],
                '99 <span class="status green">paid</span>',
            ],
            'waive requested' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_wr',
                        'description' => 'waive requested'
                    ],
                ],
                '99 <span class="status orange">waive requested</span>',
            ],
            'waived' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_w',
                        'description' => 'waived'
                    ],
                ],
                '99 <span class="status green">waived</span>',
            ],
            'cancelled' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_cn',
                        'description' => 'cancelled'
                    ],
                ],
                '99 <span class="status grey">cancelled</span>',
            ],
            'other' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'foo',
                        'description' => 'foo'
                    ],
                ],
                '99 <span class="status">foo</span>',
            ],
        ];
    }
}

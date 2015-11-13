<?php

/**
 * Fee number and status formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\FeeNoAndStatus as Sut;
use PHPUnit_Framework_TestCase;

/**
 * Fee number and status formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeNoAndStatusTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals($expected, Sut::format($data));
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
                '99<span class="status orange">outstanding</span>',
            ],
            'paid' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_pd',
                        'description' => 'paid'
                    ],
                ],
                '99<span class="status green">paid</span>',
            ],
            'cancelled' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'lfs_cn',
                        'description' => 'cancelled'
                    ],
                ],
                '99<span class="status red">cancelled</span>',
            ],
            'other' => [
                [
                    'id' => '99',
                    'feeStatus' => [
                        'id' => 'foo',
                        'description' => 'foo'
                    ],
                ],
                '99<span class="status grey">foo</span>',
            ],
        ];
    }
}

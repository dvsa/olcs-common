<?php

/**
 * PI Hearing status formatter test
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\PiHearingStatus;
use PHPUnit_Framework_TestCase;

/**
 * PI Hearing status formatter test
 */
class PiHearingStatusTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the format method
     *
     * @group Formatters
     * @group PiHearingStatusFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, PiHearingStatus::format($data));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'cancelled' => [
                [
                    'isCancelled' => 'Y',
                    'isAdjourned' => 'N',
                ],
                '<span class="status red">CNL</span>',
            ],
            'adjourned' => [
                [
                    'isCancelled' => 'N',
                    'isAdjourned' => 'Y',
                ],
                '<span class="status orange">ADJ</span>',
            ],
            'other' => [
                [
                    'isCancelled' => 'N',
                    'isAdjourned' => 'N',
                ],
                '',
            ],
        ];
    }
}

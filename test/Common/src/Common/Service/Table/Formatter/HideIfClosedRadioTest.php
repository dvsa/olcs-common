<?php

/**
 * Hide If Closed Radio Formatter Test
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\HideIfClosedRadio;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Hide If Closed Radio Formatter Test
 */
class HideIfClosedRadioTest extends MockeryTestCase
{
    /**
     * Test formatter
     *
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, HideIfClosedRadio::format($data));
    }

    public function formatProvider()
    {
        return [
            [
                [
                    'closedDate' => '2015-03-24',
                    'id' => 1
                ],
                ''
            ],
            [
                [
                    'closedDate' => '',
                    'id' => 1
                ],
                '<input type="radio" value="1" name="id">'
            ],
            [
                [
                    'id' => 1
                ],
                '<input type="radio" value="1" name="id">'
            ],
        ];
    }
}

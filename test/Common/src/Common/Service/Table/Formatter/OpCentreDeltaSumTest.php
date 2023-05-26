<?php

/**
 * OpCentreDeltaSumTest.php
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\OpCentreDeltaSum;

/**
 * Class SumTest
 *
 * OpCentreDelta sum test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OpCentreDeltaSumTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dpFormatDataProvider
     */
    public function testFormat($data, $expected)
    {
        $column = array(
            'name' => 'colName'
        );

        $this->assertEquals((new OpCentreDeltaSum())->format($data, $column), $expected);
    }

    public function dpFormatDataProvider()
    {
        return [
            [
                [
                    ['action' => 'U', 'colName' => 1],
                    ['action' => 'E', 'colName' => 3],
                    ['action' => 'A', 'colName' => 4],
                    ['action' => 'C', 'colName' => 100],
                    ['action' => 'D', 'colName' => 100]
                ],
                8
            ],
            [
                [
                    ['action' => 'C', 'colName' => 100],
                    ['action' => 'D', 'colName' => 100]
                ],
                0
            ],
            [
                [
                    ['action' => 'E', 'colName' => 3],
                    ['action' => 'A', 'colName' => 4],
                ],
                7
            ],
            [
                [
                    ['action' => 'A', 'colName' => 4],
                ],
                4
            ]
        ];
    }
}

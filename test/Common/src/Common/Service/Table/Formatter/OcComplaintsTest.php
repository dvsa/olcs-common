<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\OcComplaints;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class OcComplaintsTest
 *
 * Formatter test.
 *
 * @package CommonTest\Service\Table\Formatter
 */
class OcComplaintsTest extends TestCase
{
    /**
     * @dataProvider dpFormatDataProvider
     */
    public function testFormat($data, $complaints)
    {
        $this->assertEquals((new OcComplaints())->format($data), $complaints);
    }

    public function dpFormatDataProvider()
    {
        return [
            [
                [
                    'operatingCentre' => [
                        'complaints' => [
                            ['id' => 1],
                            ['id' => 2],
                            ['id' => 3],
                        ]
                    ]
                ],
                3
            ],
            [
                [
                    'operatingCentre'
                ],
                0
            ]
        ];
    }
}

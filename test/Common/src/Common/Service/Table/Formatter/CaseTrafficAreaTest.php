<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\CaseTrafficArea;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Service\Table\Formatter\CaseTrafficArea
 */
class CaseTrafficAreaTest extends MockeryTestCase
{
    /**
     * @dataProvider  dpTestFormat
     */
    public function testFormat($data, $expect)
    {
        static::assertSame($expect, CaseTrafficArea::format($data));
    }

    public function dpTestFormat()
    {
        return [
            'lic|app' => [
                'data' => [
                    'licence' => [
                        'trafficArea' => [
                            'name' => 'unit_TaName',
                        ],
                    ],
                ],
                'expect' => 'unit_TaName',
            ],
            'tm' => [
                'data' => [
                ],
                'expect' => CaseTrafficArea::NOT_APPLICABLE,
            ],
        ];
    }
}

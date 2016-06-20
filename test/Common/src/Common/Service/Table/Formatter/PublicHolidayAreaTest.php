<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\PublicHolidayArea;
use Mockery as m;

/**
 * @covers Common\Service\Table\Formatter\PublicHolidayArea
 */
class PublicHolidayAreaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dpTestFormat
     */
    public function testFormat($data, $expect)
    {
        static::assertEquals($expect, PublicHolidayArea::format($data));
    }

    public function dpTestFormat()
    {
        return [
            [
                'data' => [
                    'isEngland' => 'N',
                    'isNi' => 'N',
                ],
                'expect' => PublicHolidayArea::NO_AREA,
            ],
            [
                'data' => [
                    'isEngland' => 'Y',
                    'isWales' => 'Y',
                    'isScotland' => 'Y',
                    'isNi' => 'Y',
                ],
                'expect' => 'England, Wales, Scotland, Northern Ireland',
            ],
        ];
    }
}

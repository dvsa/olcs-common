<?php

/**
 * Date Timezone Fix formatter test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\DateTimezoneFix;

class DateTimezoneFixTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        if (!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', 'd/m/Y');
        }
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group DateFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $this->assertEquals($expected, DateTimezoneFix::format($data, $column));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'No Date Column' => [
                [],
                [
                    'name' => 'startDate',
                ],
                ''
            ],
            'Null Date' => [
                [
                    'startDate' => null,
                ],
                [
                    'name' => 'startDate',
                ],
                ''
            ],
            'Empty Date' => [
                [
                    'startDate' => '',
                ],
                [
                    'name' => 'startDate',
                ],
                ''
            ],
            'Valid Date No Date Format Column' => [
                [
                    'startDate' => '2018-01-01T00:00:00+0000',
                ],
                [
                    'name' => 'startDate',
                ],
                '01/01/2018 00:00',
            ],
            'Empty Date Format Column' => [
                [
                    'startDate' => '2018-01-01T00:00:00+0000',
                ],
                [
                    'dateformat' => '',
                    'name' => 'startDate',
                ],
                '01/01/2018 00:00',
            ],
            'Complete Valid Data' => [
                [
                    'startDate' => '2018-01-01T00:00:00+0000',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'startDate',
                ],
                '01/01/2018',
            ],
            'Valid Data with Late Time' => [
                [
                    'startDate' => '2018-01-01T22:00:00+0000',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'startDate',
                ],
                '01/01/2018',
            ],
            'Valid Data with Later Time' => [
                [
                    'startDate' => '2018-01-01T23:59:59+0000',
                ],
                [
                    'dateformat' => 'd/m/Y',
                    'name' => 'startDate',
                ],
                '01/01/2018',
            ],

        ];
    }
}

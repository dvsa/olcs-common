<?php

namespace CommonTest\Filter;

use PHPUnit_Framework_TestCase;
use Common\Filter\DateTimeSelectNullifier;

/**
 * Date Time Select Nullifier Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateTimeSelectNullifierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group filter
     * @group date_time_select_nullifier_filter
     * @dataProvider provideFilter
     */
    public function testFilter($input, $output)
    {
        $sut = new DateTimeSelectNullifier();
        $this->assertEquals($output, $sut->filter($input));
    }

    /**
     * @return array
     */
    public function provideFilter()
    {
        return [
            [null, null],
            ['string', null],
            [['day'=>'', 'year'=>'', 'month'=>''], null],
            [['day'=>'04', 'year'=>'2012', 'month'=>''], null],
            [['day'=>'04', 'year'=>'2012', 'month'=>'10'], null],
            [['day'=>'04', 'year'=>'2012', 'month'=>'10', 'hour' => ''], null],
            [['day'=>'04', 'year'=>'2012', 'month'=>'10', 'hour' => '16', 'minute' => ''], null],
            [['day'=>'04', 'year'=>'2012', 'month'=>'10', 'hour' => '16', 'minute' => '00'], '2012-10-04 16:00:00'],
        ];
    }
}

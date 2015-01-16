<?php

/**
 * Date Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\DateHelperService;

/**
 * Date Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateHelperServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetDateWithNoParams()
    {
        $helper = new DateHelperService();

        // as much as I don't like computed expectations in tests,
        // there's no real way round it here...
        $this->assertEquals(date('Y-m-d'), $helper->getDate());
    }

    public function testGetDateWithParams()
    {
        $helper = new DateHelperService();

        // as much as I don't like computed expectations in tests,
        // there's no real way round it here...
        $this->assertEquals(date('m-d'), $helper->getDate('m-d'));
    }

    public function testGetDateObject()
    {
        $helper = new DateHelperService();

        $this->assertInstanceOf('DateTime', $helper->getDateObject());
    }

    public function testGetDateObjectFromArray()
    {
        $helper = new DateHelperService();

        $obj = $helper->getDateObjectFromArray(
            [
                'day' => '07',
                'month' => '01',
                'year' => '2015'
            ]
        );

        $this->assertInstanceOf('DateTime', $obj);
        $this->assertEquals('2015-01-07', $obj->format('Y-m-d'));
    }
}

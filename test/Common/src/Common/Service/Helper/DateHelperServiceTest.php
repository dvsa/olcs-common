<?php

/**
 * Date Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\DateHelperService;

/**
 * Date Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DateHelperServiceTest extends MockeryTestCase
{
    public function setUp(): void
    {
        $this->sut = new DateHelperService();
    }

    public function testGetDateWithNoParams()
    {
        // as much as I don't like computed expectations in tests,
        // there's no real way round it here...
        $this->assertEquals(date('Y-m-d'), $this->sut->getDate());
    }

    public function testGetDateWithParams()
    {
        // as much as I don't like computed expectations in tests,
        // there's no real way round it here...
        $this->assertEquals(date('m-d'), $this->sut->getDate('m-d'));
    }

    public function testGetDateObject()
    {
        $this->assertInstanceOf('DateTime', $this->sut->getDateObject());
    }

    public function testGetDateObjectFromArray()
    {
        $obj = $this->sut->getDateObjectFromArray(
            [
                'day' => '07',
                'month' => '01',
                'year' => '2015'
            ]
        );

        $this->assertInstanceOf('DateTime', $obj);
        $this->assertEquals('2015-01-07', $obj->format('Y-m-d'));
    }

    public function testCalculateDate()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Common\Util\DateTimeProcessor')
            ->andReturn(
                m::mock()
                ->shouldReceive('calculateDate')
                ->with('2015-01-01', 10, true, false)
                ->andReturn('result')
                ->getMock()
            )
            ->getMock();

        $this->sut->setServiceLocator($sm);

        $this->assertEquals(
            'result',
            $this->sut->calculateDate('2015-01-01', 10, true, false)
        );
    }
}

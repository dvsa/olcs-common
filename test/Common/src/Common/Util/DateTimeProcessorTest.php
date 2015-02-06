<?php
namespace CommonTest\Util;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Util\DateTimeProcessor as DateTimeProcessor;
use Common\Service\Data\PublicHoliday as PublicHolidayService;
use Mockery as m;

/**
 * Test Api resolver
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class DateTimeProcessorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpCalculateDate
     *
     * @param \DateTime|string $inDate
     * @param integer $days
     * @param boolean $we
     * @param boolean $bh
     * @param \DateTime|string $outDate
     */
    public function testCalculateDate($inDate, $days, $we, $ph, $outDate, $phDates = null)
    {
        date_default_timezone_set('UTC');

        $sut = new DateTimeProcessor();

        $mock = m::mock('Common\Service\Data\PublicHoliday');
        $mock->shouldReceive('fetchPublicHolidaysArray')
             ->andReturn($phDates);

        // Dataservice manager
        $ds = new \Zend\ServiceManager\ServiceManager();
        $ds->setService('Common\Service\Data\PublicHoliday', $mock);

        // Zend service locator
        $sl = new \Zend\ServiceManager\ServiceManager();
        $sl->setService('DataServiceManager', $ds);

        $sut->createService($sl);

        $calculatedDate = $sut->calculateDate($inDate, $days, $we, $ph);

        $this->assertEquals($outDate, $calculatedDate);
    }

    public function dpCalculateDate()
    {
        return [
           [
                '2014-11-03',
                '11',
                false, // weekends
                false,
                '2014-11-14'
            ],
            [ // weekeds skipped
                '2014-11-03',
                '9',
                true, // weekends
                false, // public holidays
                '2014-11-14'
            ],
            [ // no days skipped
                '2014-04-28',
                '17',
                false, // weekends
                false, // public holidays
                '2014-05-15'
            ],
            [ // weekends and public holidays skipped
                '2014-04-28',
                '13',
                true, // weekends
                false, // public holidays
                '2014-05-15'
            ],
            [ // no weekends but public holidays are skipped
                '2014-12-22',
                '12',
                false, // weekends
                true, // public holidays
                '2015-01-06',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                ]
            ],
            [ // weekends and public holidays are skipped
                '2014-12-22',
                '8',
                true, // weekends
                true, // public holidays
                '2015-01-06',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                ]
            ],
            [ // negative, no weekends but public holidays are skipped
                '2015-01-03',
                '-8',
                false, // weekends
                true, // public holidays
                '2014-12-24',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                ]
            ],
            [ // negative, weekends and public holidays are skipped
                '2015-01-03',
                '-6',
                true, // weekends
                true, // public holidays
                '2014-12-10',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                ]
            ],
            [ // negative, none are skipped
                '2015-01-03',
                '-20',
                false, // weekends
                false, // public holidays
                '2014-12-14',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                ]
            ],
            [ // reproduces a bug
                '2014-12-02',
                '30',
                true, // weekends
                true, // public holidays
                '2015-01-16',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                ]
            ],
            [
                '2015-12-01',
                '18',
                true, // weekends
                true, // public holidays
                '2015-12-29',
                [
                    '2015-12-25',
                    '2015-12-26'
                ]
            ],
            [ // reproduces a bug on Christmas day - sorted by date_default_timezone_set('UTC');
                '2014-10-02',
                '60',
                true, // weekends
                true, // public holidays
                '2014-12-29',
                [
                    '2014-12-25',
                    '2014-12-26'
                ]
            ],
            [ // easter sunday problem
                '2015-04-28',
                '-14',
                true, // weekends
                true, // public holidays
                '2015-04-02',
                [
                    '2015-04-06',
                    '2015-04-03'
                ]
            ],
            [ // PI agreed date for extra BH in scotland
                '2014-12-12',
                '17',
                true, // weekends
                true, // public holidays
                '2015-01-12',
                [
                    '2014-12-25',
                    '2014-12-26',
                    '2015-01-01',
                    '2015-01-02',
                ]
            ]
        ];
    }
}

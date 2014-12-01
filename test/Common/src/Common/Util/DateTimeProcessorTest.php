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

        /* $this->assertInstanceOf('Common\Util\DateTimeProcessor\Positive', $sut->getPositiveProcessor());
        $this->assertInstanceOf('Common\Util\DateTimeProcessor\Negative', $sut->getNegativeProcessor()); */

        $this->assertEquals($outDate, $sut->calculateDate($inDate, $days, $we, $ph));
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
            [ // netagive, no weekends but public holidays are skipped
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
            [ // netagive, weekends and public holidays are skipped
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
            [ // netagive, none are skipped
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
            ]
        ];
    }
}

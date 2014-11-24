<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\PublicHoliday;
use Mockery as m;
use Common\Service\Data\Licence as LicenceService;

/**
 * Class PublicHoliday Test
 * @package CommonTest\Service\Data
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PublicHolidayTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServiceName()
    {
        $sut = new PublicHoliday();
        $this->assertEquals('PublicHoliday', $sut->getServiceName());
    }

    public function testFecthPublicHolidaysArray()
    {
        $startDate = \DateTime::createFromFormat('Y-m-d', '2014-01-01');
        $endDate = \DateTime::createFromFormat('Y-m-d', '2014-01-14');

        $params = [
            'isEngland' => '1',
            'publicHolidayDate' => '=>2014-01-01',
            'publicHolidayDate' => '<=2014-01-28',
            'limit' => '10000'
        ];

        $returnData = [
            'Results' => [
                0 => ['publicHolidayDate' => '2014-01-05'],
                1 => ['publicHolidayDate' => '2014-01-10']
            ]
        ];

        $finalData = [
            '2014-01-05',
            '2014-01-10'
        ];

        $licenceData = [
            'id' => '1',
            'trafficArea' => [
                'isEngland' => '1',
                'isWales' => '0',
            ]
        ];

        $licence = m::mock('Common\Service\Data\Licence');
        $licence
            ->shouldReceive('fetchLicenceData')
            ->once()
            ->with()
            ->andReturn($licenceData);

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', $params)
            ->andReturn($returnData);

        $sut = new PublicHoliday();
        $sut->setRestClient($mockRestClient);
        $sut->setLicenceService($licence);

        $this->assertEquals($finalData, $sut->fetchPublicHolidaysArray($startDate, $endDate));
    }

    public function testGetListReturnsNull()
    {
        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', [])
            ->andReturn([]);

        $sut = new PublicHoliday();
        $sut->setRestClient($mockRestClient);

        $this->assertNull($sut->getList([]));
    }

    public function testGetSetLicenceService()
    {
        $licenceService = new LicenceService();

        $sut = new PublicHoliday();
        $this->assertSame($licenceService, $sut->setLicenceService($licenceService)->getLicenceService());
    }
}

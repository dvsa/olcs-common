<?php

namespace CommonTest\Service;

use Common\Service\ShortNotice;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Mockery as m;

/**
 * Class ShortNoticeTest
 * @package CommonTest\Service
 */
class ShortNoticeTest extends TestCase
{
    public function testCreateService()
    {
        $mockDataService = m::mock('Common\Service\Data\Interfaces\DataService');
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('DataServiceManager')->andReturnSelf();
        $mockSl->shouldReceive('get')->with('Generic\Service\Data\BusNoticePeriod')->andReturn($mockDataService);

        $sut = new ShortNotice();

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('Common\Service\ShortNotice', $service);
        $this->assertSame($mockDataService, $sut->getNoticePeriodService());
    }

    /**
     * @dataProvider provideIsShortNotice
     * @param $data
     * @param $rules
     * @param $result
     */
    public function testIsShortNotice($data, $rules, $result)
    {
        $mockDataService = m::mock('Common\Service\Data\Interfaces\DataService');
        $mockDataService->shouldReceive('fetchOne')->with($data['busNoticePeriod'])->andReturn($rules);

        $sut = new ShortNotice();
        $sut->setNoticePeriodService($mockDataService);

        $this->assertEquals($result, $sut->isShortNotice($data));
    }

    public function provideIsShortNotice()
    {
        $data = [
            'busNoticePeriod' => 1,
            'effectiveDate' => '2015-01-15',
            'receivedDate' => '2015-01-05'
        ];
        return [
            [
                $data,//data
                ['standardPeriod' => 20], //rules
                true, //result
            ],
            [
                $data,//data
                ['standardPeriod' => 2, 'cancellationPeriod' => 0], //rules
                false, //result
            ],
            [
                array_merge($data, ['variationNo' => 0]),//data
                ['standardPeriod' => 2, 'cancellationPeriod' => 20], //rules
                false, //result
            ],
            [
                array_merge($data, ['variationNo' => 1]),//data
                ['standardPeriod' => 2, 'cancellationPeriod' => 20], //rules
                null, //result
            ],
            [
                array_merge($data, ['variationNo' => 1, 'parent' => ['effectiveDate' => '2014-12-30']]),//data
                ['standardPeriod' => 2, 'cancellationPeriod' => 20], //rules
                true, //result
            ],
            [
                array_merge($data, ['variationNo' => 1, 'parent' => ['effectiveDate' => '2014-11-30']]),//data
                ['standardPeriod' => 2, 'cancellationPeriod' => 20], //rules
                false, //result
            ]
        ];
    }
}

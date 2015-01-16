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
    public function testIsShortNotice($rules, $data, $result)
    {
        $data['busNoticePeriod'] = 1;
        $mockDataService = m::mock('Common\Service\Data\Interfaces\DataService');
        $mockDataService->shouldReceive('fetchOne')->with($data['busNoticePeriod'])->andReturn($rules);

        $sut = new ShortNotice();
        $sut->setNoticePeriodService($mockDataService);

        $this->assertEquals($result, $sut->isShortNotice($data));
    }

    public function provideIsShortNotice()
    {
        $scotRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 90
        ];

        $otherRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 0
        ];

        $sn = true;
        $notSn = false;

        return [
            [
                $otherRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-01'],
                $sn
            ],
            [
                $otherRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-26'],
                $sn
            ],
            [
                $otherRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-27'],
                $notSn
            ],
            [
                $otherRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-08-28'],
                $notSn
            ],
            [
                $otherRules,
                ['variationNo' => 1, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-01'],
                $sn
            ],
            [
                $otherRules,
                ['variationNo' => 1, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-26'],
                $sn
            ],
            [
                $otherRules,
                ['variationNo' => 1, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-27'],
                $notSn
            ],
            [
                $otherRules,
                ['variationNo' => 1, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-08-28'],
                $notSn
            ],
            //S2
            [
                $scotRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-01'],
                $sn
            ],
            [
                $scotRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-26'],
                $sn
            ],
            [
                $scotRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-07-27'],
                $notSn
            ],
            [
                $scotRules,
                ['variationNo' => 0, 'receivedDate' => '2014-05-31', 'effectiveDate' => '2014-08-28'],
                $notSn
            ],
            //S3
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-15',
                    'effectiveDate' => '2014-07-21',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-15',
                    'effectiveDate' => '2014-09-08',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-15',
                    'effectiveDate' => '2014-09-09',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-15',
                    'effectiveDate' => '2014-09-10',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $notSn
            ],
            //S4
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-08-01',
                    'effectiveDate' => '2014-08-12',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-08-01',
                    'effectiveDate' => '2014-09-25',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-08-01',
                    'effectiveDate' => '2014-09-26',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-08-01',
                    'effectiveDate' => '2014-09-30',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $notSn
            ],
            //S5
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-01',
                    'effectiveDate' => '2014-08-12',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-01',
                    'effectiveDate' => '2014-09-08',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-01',
                    'effectiveDate' => '2014-09-09',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $sn
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-07-01',
                    'effectiveDate' => '2014-09-30',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                $notSn
            ],
        ];
    }
}

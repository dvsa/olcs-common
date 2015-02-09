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

        $this->assertSame($result, $sut->isShortNotice($data));
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
            //error cases
            [
                $otherRules,
                [
                    'receivedDate' => '',
                    'effectiveDate' => '2014-09-30'
                ],
                false
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09',
                    'effectiveDate' => '2016-09-30',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                false
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2014-06-11',
                    'effectiveDate' => '2014-08-11',
                ],
                null
            ],
        ];
    }

    /**
     * @dataProvider provideCalculateNoticeDate
     * @param $data
     * @param $rules
     * @param $result
     */
    public function testCalculateNoticeDate($rules, $data, $result)
    {
        $data['busNoticePeriod'] = 1;
        $mockDataService = m::mock('Common\Service\Data\Interfaces\DataService');
        $mockDataService->shouldReceive('fetchOne')->with($data['busNoticePeriod'])->andReturn($rules);

        $sut = new ShortNotice();
        $sut->setNoticePeriodService($mockDataService);

        $this->assertEquals($result, $sut->calculateNoticeDate($data));
    }

    public function provideCalculateNoticeDate()
    {
        $scotRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 90
        ];

        $otherRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 0
        ];

        $noRules = [
            'standardPeriod' => 0,
            'cancellationPeriod' => 0
        ];

        return [
            [
              $otherRules,
                ['receivedDate' => ''],
                null
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09'
                ],
                null
            ],
            [
                $noRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09',
                    'effectiveDate' => '2015-03-31'
                ],
                '2015-03-31'
            ],
            [
                $otherRules,
                [
                    'variationNo' => 0,
                    'receivedDate' => '2015-02-09'
                ],
                '2015-04-06'
            ],
            [
                $otherRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09'
                ],
                '2015-04-06'
            ],
            [
                $scotRules,
                [
                    'variationNo' => 0,
                    'receivedDate' => '2015-02-09'
                ],
                '2015-04-06'
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                '2014-09-09'
            ],
        ];
    }

    public function testCalculateNoticeDateNoRulesDefined()
    {
        list ($data, $result) = [
            [
                'variationNo' => 1,
                'receivedDate' => '2015-02-09',
                'parent' => ['effectiveDate' => '2014-06-11']
            ],
            false
        ];

        $sut = new ShortNotice();
        $this->assertEquals($result, $sut->calculateNoticeDate($data));
    }

    public function testIsShortNoticeNoRulesDefined()
    {
        list ($data, $result) = [
            [
                'variationNo' => 1,
                'receivedDate' => '2015-02-09',
                'effectiveDate' => '2014-06-11',
                'parent' => ['effectiveDate' => '2014-06-11']
            ],
            false
        ];

        $sut = new ShortNotice();
        $this->assertEquals($result, $sut->isShortNotice($data));
    }
}

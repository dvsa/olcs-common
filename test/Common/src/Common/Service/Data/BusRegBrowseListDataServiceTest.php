<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\BusRegBrowseListDataService;
use CommonTest\Service\Data\AbstractDataServiceTestCase;
use Dvsa\Olcs\Transfer\Query\Bus\BusRegBrowseContextList as BusRegBrowseContextListQry;
use Mockery as m;

/**
 * Class BusRegBrowseListDataServiceTest
 * @package OlcsTest\Service\Data
 */
class BusRegBrowseListDataServiceTest extends AbstractDataServiceTestCase
{
    /**
     * @dataProvider provideFetchListOptions
     */
    public function testFetchListOptions($context, $result, $expected)
    {
        $sut = new BusRegBrowseListDataService();
        $sut->setData('BusRegBrowse' . ucfirst($context), $result);

        $this->assertEquals($expected, $sut->fetchListOptions($context));
    }

    public function provideFetchListOptions()
    {
        return [
            [
                'eventRegistrationStatus',
                false,
                []
            ],
            [
                'eventRegistrationStatus',
                [
                    ['eventRegistrationStatus' => 'A'],
                    ['eventRegistrationStatus' => 'B'],
                    ['eventRegistrationStatus' => 'C'],
                ],
                [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideFetchListData
     */
    public function testFetchListData($context, $result, $expected)
    {
        $params = [
            'context' => $context,
            'sort' => $context,
            'order' => 'ASC'
        ];

        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertInstanceOf(BusRegBrowseContextListQry::class, $dto);
                    $this->assertEquals($params['context'], $dto->getContext());
                    $this->assertEquals($params['sort'], $dto->getSort());
                    $this->assertEquals($params['order'], $dto->getOrder());
                    return 'query';
                }
            )
            ->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn(['result' => $result])
            ->once()
            ->getMock();

        $sut = new BusRegBrowseListDataService();
        $sut->setData('BusRegBrowse' . ucfirst($context), null);

        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($expected, $sut->fetchListData($context));
        // test caching as well
        $this->assertEquals($expected, $sut->fetchListData($context));
    }

    public function provideFetchListData()
    {
        return [
            [
                'eventRegistrationStatus',
                [
                    ['eventRegistrationStatus' => 'A'],
                    ['eventRegistrationStatus' => 'B'],
                    ['eventRegistrationStatus' => 'C'],
                ],
                [
                    ['eventRegistrationStatus' => 'A'],
                    ['eventRegistrationStatus' => 'B'],
                    ['eventRegistrationStatus' => 'C'],
                ]
            ],
        ];
    }

    public function testFetchListDataThrowsException()
    {
        $this->expectException(\Common\Exception\DataServiceException::class);

        $context = 'eventRegistrationStatus';

        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')
            ->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();

        $sut = new BusRegBrowseListDataService();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchListData($context);
    }
}

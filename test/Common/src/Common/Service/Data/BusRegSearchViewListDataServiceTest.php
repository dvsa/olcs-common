<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\BusRegSearchViewListDataService;
use Dvsa\Olcs\Transfer\Query\BusRegSearchView\BusRegSearchViewContextList as BusRegSearchViewContextListQry;
use Mockery as m;
use CommonTest\Service\Data\AbstractDataServiceTestCase;

/**
 * Class BusRegSearchViewListDataServiceTest
 * @package OlcsTest\Service\Data
 */
class BusRegSearchViewListDataServiceTest extends AbstractDataServiceTestCase
{
    /**
     * @dataProvider provideFetchListOptions
     * @param $context
     * @param $mockResultData
     * @param $expected
     */
    public function testFetchListOptions($context, $mockResultData, $expected)
    {
        $sut = new BusRegSearchViewListDataService();
        $sut->setData('BusRegSearchView' . ucfirst($context), $mockResultData);

        $this->assertEquals($expected, $sut->fetchListOptions($context));
    }

    public function testFetchListOptionsInvalidContext()
    {
        $this->expectException(\Common\Exception\DataServiceException::class);

        $context = 'invalid';
        $sut = new BusRegSearchViewListDataService();

        $sut->fetchListOptions($context);
    }

    /**
     * @dataProvider provideFetchListData
     */
    public function testFetchListData($context, $expected)
    {
        $params = [
            'context' => $context,
            'order' => 'ASC'
        ];
        $dto = BusRegSearchViewContextListQry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['context'], $dto->getContext());
                    return 'query';
                }
            )
            ->once()
            ->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn(['results' => $expected])
            ->once()
            ->getMock();

        $sut = new BusRegSearchViewListDataService();
        $sut->setData('BusRegSearchView' . ucfirst($context), null);

        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($expected, $sut->fetchListData($context));
    }

    public function provideFetchListOptions()
    {
        return [
            [
                'licence',
                [
                    0 => ['licNo' => 'UB1234', 'licId' => '111'],
                    1 => ['licNo' => 'UB1235', 'licId' => '222'],
                    2 => ['licNo' => 'UB1236', 'licId' => '333']
                ],
                [
                    111 => 'UB1234',
                    222 => 'UB1235',
                    333 => 'UB1236'
                ]
            ],
            [
                'organisation',
                [
                    0 => ['organisationName' => 'ABC Ltd', 'organisationId' => '111'],
                    1 => ['organisationName' => 'CDE Ltd', 'organisationId' => '222'],
                    2 => ['organisationName' => 'FGH Ltd', 'organisationId' => '333']
                ],
                [
                    111 => 'ABC Ltd',
                    222 => 'CDE Ltd',
                    333 => 'FGH Ltd'
                ]
            ],

            [
                'busRegStatus',
                [
                    0 => ['busRegStatusDesc' => 's1', 'busRegStatus' => '111'],
                    1 => ['busRegStatusDesc' => 's2', 'busRegStatus' => '222'],
                    2 => ['busRegStatusDesc' => 's3', 'busRegStatus' => '333']
                ],
                [
                    111 => 's1',
                    222 => 's2',
                    333 => 's3'
                ]
            ],
        ];
    }

    public function provideFetchListData()
    {
        return [
            [
                'licence',
                [
                    0 => ['licNo' => 'UB1234', 'licId' => '111'],
                    1 => ['licNo' => 'UB1235', 'licId' => '222'],
                    2 => ['licNo' => 'UB1236', 'licId' => '333']
                ]
            ],
            [
                'organisation',
                [
                    0 => ['organisationName' => 'ABC Ltd', 'organisationId' => '111'],
                    1 => ['organisationName' => 'CDE Ltd', 'organisationId' => '222'],
                    2 => ['organisationName' => 'FGH Ltd', 'organisationId' => '333']
                ]
            ],

            [
                'busRegStatus',
                [
                    0 => ['busRegStatusDesc' => 's1', 'busRegStatus' => '111'],
                    1 => ['busRegStatusDesc' => 's2', 'busRegStatus' => '222'],
                    2 => ['busRegStatusDesc' => 's3', 'busRegStatus' => '333']
                ]
            ]
        ];
    }
}

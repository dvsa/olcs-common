<?php

namespace CommonTest\Common\Service\Data;

use Common\Exception\DataServiceException;
use Common\Service\Data\BusRegBrowseListDataService;
use Dvsa\Olcs\Transfer\Query\Bus\BusRegBrowseContextList as Qry;
use Mockery as m;

/**
 * Class BusRegBrowseListDataServiceTest
 * @package OlcsTest\Service\Data
 */
class BusRegBrowseListDataServiceTest extends AbstractDataServiceTestCase
{
    /** @var BusRegBrowseListDataService */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new BusRegBrowseListDataService($this->abstractDataServiceServices);
    }

    /**
     * @dataProvider provideFetchListOptions
     */
    public function testFetchListOptions($context, $result, $expected)
    {
        $this->sut->setData('BusRegBrowse' . ucfirst($context), $result);

        $this->assertEquals($expected, $this->sut->fetchListOptions($context));
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

        $this->transferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(Qry::class))
            ->once()
            ->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['context'], $dto->getContext());
                    $this->assertEquals($params['sort'], $dto->getSort());
                    $this->assertEquals($params['order'], $dto->getOrder());
                    return $this->query;
                }
            );

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn(['result' => $result])
            ->once()
            ->getMock();

        $this->sut->setData('BusRegBrowse' . ucfirst($context), null);

        $this->mockHandleQuery($mockResponse);

        $this->assertEquals($expected, $this->sut->fetchListData($context));
        // test caching as well
        $this->assertEquals($expected, $this->sut->fetchListData($context));
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
        $this->expectException(DataServiceException::class);

        $context = 'eventRegistrationStatus';

        $this->transferAnnotationBuilder->shouldReceive('createQuery')
            ->with(m::type(Qry::class))
            ->once()
            ->andReturn($this->query);

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();

        $this->mockHandleQuery($mockResponse);

        $this->sut->fetchListData($context);
    }
}

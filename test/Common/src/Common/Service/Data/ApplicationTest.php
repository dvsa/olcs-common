<?php

namespace OlcsTest\Service\Data;

use Common\Exception\DataServiceException;
use CommonTest\Service\Data\AbstractDataServiceTestCase;
use Common\Service\Data\Application;
use Dvsa\Olcs\Transfer\Query\Application\Application as ApplicationQry;
use Dvsa\Olcs\Transfer\Query\Application\OperatingCentres as OcQry;
use Common\RefData as CommonRefData;
use Mockery as m;

/**
 * Class Application Test
 * @package CommonTest\Service
 */
class ApplicationTest extends AbstractDataServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Data\Application
     */
    private $sut;

    /**
     * Setup the sut
     */
    protected function setUp(): void
    {
        $this->sut = new Application();
    }

    public function testSetId()
    {
        $this->sut->setId(78);
        $this->assertEquals(78, $this->sut->getId());
    }

    public function testGetId()
    {
        $this->assertNull($this->sut->getId());
    }

    public function testFetchData()
    {
        $id = 1;
        $data = ['id' => 99];
        $this->sut->setData(1, $data);

        $result = $this->sut->fetchData($id);

        $this->assertEquals($data, $result);
    }

    /**
     * Test canHaveCases
     *
     * @dataProvider canHaveCasesDataProvider
     * @param array $application
     * @param int $expectedResult
     */
    public function testCanHaveCases($application, $expectedResult)
    {
        $this->sut->setData($application['id'], $application);
        $this->assertEquals($expectedResult, $this->sut->canHaveCases($application['id']));
    }

    public function testFetchOperatingCentreData()
    {
        $application = [
            'id' => 78,
            'operatingCentres' => [
                'operatingCentre' => 'oc',
            ],
        ];

        $params = [
            'id' => 78
        ];
        $dto = OcQry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['id'], $dto->getId());
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
            ->andReturn($application)
            ->once()
            ->getMock();

        $sut = new Application();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($application, $sut->fetchOperatingCentreData(78));
    }

    public function testFetchOperatingCentreDataWithException()
    {
        $this->expectException(DataServiceException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new Application();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchOperatingCentreData(78);
    }

    /**
     * Data provider for canHaveCases.
     *
     * @return array
     */
    public function canHaveCasesDataProvider()
    {
        return [
            // status / licence not set
            [
                ['id' => 100],
                false
            ],
            // licence without licNo
            [
                [
                    'id' => 100,
                    'licence' => ['licNo' => null]
                ],
                false
            ],
            // status NOT_SUBMITTED
            [
                [
                    'id' => 100,
                    'status' => ['id' => CommonRefData::APPLICATION_STATUS_NOT_SUBMITTED]
                ],
                false
            ],
            // licence with licNo and status different than NOT_SUBMITTED
            [
                [
                    'id' => 100,
                    'status' => ['id' => CommonRefData::APPLICATION_STATUS_GRANTED],
                    'licence' => ['licNo' => 'ABC']
                ],
                true
            ],
        ];
    }

    public function testFetchApplicationData()
    {
        $application = ['foo' => 'bar'];

        $params = [
            'id' => 78
        ];
        $dto = ApplicationQry::create($params);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['id'], $dto->getId());
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
            ->andReturn($application)
            ->once()
            ->getMock();

        $sut = new Application();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($application, $sut->fetchApplicationData(78));
    }

    public function testFetchApplicationDataWithException()
    {
        $this->expectException(DataServiceException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new Application();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchApplicationData(78);
    }
}

<?php


namespace CommonTest\Service\Data;

use Common\Exception\DataServiceException;
use Common\Service\Data\Surrender;
use Dvsa\Olcs\Transfer\Query\Surrender\ByLicence as SurrenderQry;

class SurrenderTest extends AbstractDataServiceTestCase
{
    public function setUp(): void
    {
        $this->sut = new Surrender();
    }

    public function testFetchSurrender()
    {
        $params = ['id' => 7];
        $expected = [

        ];
        $dto = SurrenderQry::create($params);
        $mockTransferAnnotationBuilder = \Mockery::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['id'], $dto->getId());
                    return 'query';
                }
            )
            ->once()
            ->getMock();

        $mockResponse = \Mockery::mock()
            ->shouldReceive('isOk')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getResult')
            ->andReturn($expected)
            ->once()
            ->getMock();

        $this->mockHandleQuery($this->sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($expected, $this->sut->fetchSurrenderData(7));
    }

    public function testThrowsExceptionIfNot200Response()
    {
        $params = ['id' => 7];
        $this->expectException(DataServiceException::class);
        $dto = SurrenderQry::create($params);
        $mockTransferAnnotationBuilder = \Mockery::mock()
            ->shouldReceive('createQuery')->once()->andReturnUsing(
                function ($dto) use ($params) {
                    $this->assertEquals($params['id'], $dto->getId());
                    return 'query';
                }
            )
            ->once()
            ->getMock();

        $mockResponse = \Mockery::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();

        $this->mockHandleQuery($this->sut, $mockTransferAnnotationBuilder, $mockResponse);
        $this->sut->fetchSurrenderData(7);
    }
}

<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\Licence;
use Mockery as m;
use Dvsa\Olcs\Transfer\Query\Licence\Licence as LicenceQry;
use Dvsa\Olcs\Transfer\Query\Licence\OperatingCentres as OcQry;
use Common\Service\Entity\Exceptions\UnexpectedResponseException;
use CommonTest\Service\Data\AbstractDataServiceTestCase;

/**
 * Class LicenceTest
 * @package OlcsTest\Service\Data
 */
class LicenceTest extends AbstractDataServiceTestCase
{
    public function testSetId()
    {
        $sut = new Licence();
        $sut->setId(78);
        $this->assertEquals(78, $sut->getId());
    }

    public function testGetId()
    {
        $sut = new Licence();
        $this->assertNull($sut->getId());
    }

    public function testFetchLicenceData()
    {
        $licence = [
            'id' => 78,
            'trafficArea' => [
                'id' => 'B',
                'isNi' => true
            ],
            'niFlag' => 'Y'
        ];

        $expected = [
            'id' => 78,
            'trafficArea' => [
                'id' => 'B',
                'isNi' => true
            ],
            'niFlag' => 'Y',
        ];

        $params = [
            'id' => 78
        ];
        $dto = LicenceQry::create($params);
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
            ->andReturn($licence)
            ->once()
            ->getMock();

        $sut = new Licence();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($expected, $sut->fetchLicenceData(78));
    }

    public function testFetchLicenceDataWithoutId()
    {
        $sut = new Licence();
        $this->assertEquals([], $sut->fetchLicenceData());
    }

    public function testFetchLicenceDataWithException()
    {
        $this->expectException(UnexpectedResponseException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new Licence();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchLicenceData(78);
    }

    public function testFetchOperatingCentreData()
    {
        $licence = [
            'id' => 78,
            'operatingCentres' => [
                'operatingCentre' => 'oc',
            ],
        ];

        $expected = [
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
            ->andReturn($licence)
            ->once()
            ->getMock();

        $sut = new Licence();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $this->assertEquals($expected, $sut->fetchOperatingCentreData(78));
    }

    public function testFetchOperatingCentresDataWithException()
    {
        $this->expectException(UnexpectedResponseException::class);
        $mockTransferAnnotationBuilder = m::mock()
            ->shouldReceive('createQuery')->once()->andReturn('query')->getMock();

        $mockResponse = m::mock()
            ->shouldReceive('isOk')
            ->andReturn(false)
            ->once()
            ->getMock();
        $sut = new Licence();
        $this->mockHandleQuery($sut, $mockTransferAnnotationBuilder, $mockResponse);

        $sut->fetchOperatingCentreData(78);
    }
}

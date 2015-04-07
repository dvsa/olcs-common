<?php

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\DeleteGoodsVehicle;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Business Details Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteGoodsVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $bsm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new DeleteGoodsVehicle();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessWithFail()
    {
        $params = [
            'ids' => [111, 222, 333]
        ];

        // Mocks
        $mockCeaseActiveDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\CeaseActiveDisc', $mockCeaseActiveDisc);

        $mockResponse = new Response();
        $mockResponse->setType(Response::TYPE_FAILED);

        // Expectations
        $mockCeaseActiveDisc->shouldReceive('process')
            ->once()
            ->with(['id' => 111])
            ->andReturn($mockResponse);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcess()
    {
        $params = [
            'ids' => [111, 222, 333]
        ];

        // Mocks
        $mockLicenceVehicle = m::mock();
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        $mockCeaseActiveDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\CeaseActiveDisc', $mockCeaseActiveDisc);

        $mockResponse = new Response();
        $mockResponse->setType(Response::TYPE_SUCCESS);

        // Expectations
        $mockCeaseActiveDisc->shouldReceive('process')
            ->once()
            ->with(['id' => 111])
            ->andReturn($mockResponse)
            ->shouldReceive('process')
            ->once()
            ->with(['id' => 222])
            ->andReturn($mockResponse)
            ->shouldReceive('process')
            ->once()
            ->with(['id' => 333])
            ->andReturn($mockResponse);

        $mockLicenceVehicle->shouldReceive('delete')
            ->once()
            ->with(111)
            ->shouldReceive('delete')
            ->once()
            ->with(222)
            ->shouldReceive('delete')
            ->once()
            ->with(333);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}

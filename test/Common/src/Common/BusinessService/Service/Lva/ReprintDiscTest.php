<?php

/**
 * Reprint Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\ReprintDisc;
use Common\BusinessService\Response;

/**
 * Reprint Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReprintDiscTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    public function setUp()
    {
        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new ReprintDisc();
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessWithCeaseDiscFail()
    {
        $params = [
            'ids' => [111, 222, 333]
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockCeaseActiveDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockRequestDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\CeaseActiveDisc', $mockCeaseActiveDisc);
        $this->bsm->setService('Lva\RequestDisc', $mockRequestDisc);

        // Expectations
        $mockCeaseActiveDisc->shouldReceive('process')
            ->once()
            ->with(['id' => 111])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(false);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse, $response);
    }

    public function testProcessWithRequestDiscFail()
    {
        $params = [
            'ids' => [111, 222, 333]
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockResponse2 = m::mock();
        $mockCeaseActiveDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockRequestDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\CeaseActiveDisc', $mockCeaseActiveDisc);
        $this->bsm->setService('Lva\RequestDisc', $mockRequestDisc);

        // Expectations
        $mockCeaseActiveDisc->shouldReceive('process')
            ->once()
            ->with(['id' => 111])
            ->andReturn($mockResponse);

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $mockRequestDisc->shouldReceive('process')
            ->once()
            ->with(['licenceVehicle' => 111, 'isCopy' => 'Y'])
            ->andReturn($mockResponse2);

        $mockResponse2->shouldReceive('isOk')
            ->andReturn(false);

        $response = $this->sut->process($params);

        $this->assertSame($mockResponse2, $response);
    }

    public function testProcess()
    {
        $params = [
            'ids' => [111, 222, 333]
        ];

        // Mocks
        $mockResponse = m::mock();
        $mockCeaseActiveDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $mockRequestDisc = m::mock('\Common\BusinessService\BusinessServiceInterface');

        $this->bsm->setService('Lva\CeaseActiveDisc', $mockCeaseActiveDisc);
        $this->bsm->setService('Lva\RequestDisc', $mockRequestDisc);

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

        $mockResponse->shouldReceive('isOk')
            ->andReturn(true);

        $mockRequestDisc->shouldReceive('process')
            ->once()
            ->with(['licenceVehicle' => 111, 'isCopy' => 'Y'])
            ->andReturn($mockResponse)
            ->shouldReceive('process')
            ->once()
            ->with(['licenceVehicle' => 222, 'isCopy' => 'Y'])
            ->andReturn($mockResponse)
            ->shouldReceive('process')
            ->once()
            ->with(['licenceVehicle' => 333, 'isCopy' => 'Y'])
            ->andReturn($mockResponse);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}

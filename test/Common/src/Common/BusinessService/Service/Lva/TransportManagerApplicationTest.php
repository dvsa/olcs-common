<?php

/**
 * Transport Manager Application Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\TransportManagerApplication;
use Common\BusinessService\Response;

/**
* Transport Manager Application Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
  */
class TransportManagerApplicationTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new TransportManagerApplication();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessMissingData()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $this->sut->process(['foo']);
    }

    public function testProcessCreateSuccess()
    {
        $mockTmaEntityService = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTmaEntityService);

        $mockTmaEntityService->shouldReceive('save')->once()->with(['foo' => 1])->andReturn(['id' => 65]);

        $response = $this->sut->process(['data' => ['foo' => 1]]);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 65], $response->getData());
    }

    public function testProcessUpdateSuccess()
    {
        $mockTmaEntityService = m::mock();
        $this->sm->setService('Entity\TransportManagerApplication', $mockTmaEntityService);

        $mockTmaEntityService->shouldReceive('save')->once()->with(['foo' => 1, 'id' => 33])->andReturn(null);

        $response = $this->sut->process(['data' => ['foo' => 1, 'id' => 33]]);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 33], $response->getData());
    }
}


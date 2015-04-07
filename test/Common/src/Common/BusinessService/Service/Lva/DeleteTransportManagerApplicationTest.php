<?php

/**
 * Transport Manager Application Delete test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\BusinessService\Service\TransportManagerApplication;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\DeleteTransportManagerApplication;
use Common\BusinessService\Response;

/**
 * Transport Manager Application Delete test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteTransportManagerApplicationTest extends MockeryTestCase
{
    protected $sm;
    protected $sut;
    protected $bsm;

    public function setUp()
    {
        $this->sm = \CommonTest\Bootstrap::getServiceManager();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut = new DeleteTransportManagerApplication();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessWithMissingParam()
    {
        $params = ['foo' => 'bar'];
        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_FAILED, $response->getType());
    }

    public function testProcessWithOneId()
    {
        $mockService = m::mock('\Common\Service\Entity\TransportManagerApplicationEntityService');
        $this->sm->setService('Entity\TransportManagerApplication', $mockService);

        $mockService->shouldReceive('delete')
            ->once()
            ->with([762]);

        $params = ['ids' => 762];
        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }

    public function testProcessWithMultipleIds()
    {
        $mockService = m::mock('\Common\Service\Entity\TransportManagerApplicationEntityService');
        $this->sm->setService('Entity\TransportManagerApplication', $mockService);

        $mockService->shouldReceive('delete')
            ->once()
            ->with([762, 65, 3431]);

        $params = ['ids' => [762, 65, 3431]];
        $response = $this->sut->process($params);

        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}

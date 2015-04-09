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
use Common\BusinessService\Service\Lva\DeleteTransportManager;
use Common\BusinessService\Response;

/**
* Transport Manager Application Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
  */
class DeltaDeleteTransportManagerLicenceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new DeleteTransportManager();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();
        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcessMissingApplication()
    {
        $this->setExpectedException('\InvalidArgumentException', 'params key "applicationId" must be set');
        $this->sut->process(['foo']);
    }

    public function testProcessMissingTransportManager()
    {
        $this->setExpectedException('\InvalidArgumentException', 'params key "transportManager" must be set');
        $this->sut->process(['applicationId' => 12]);
    }

    public function testProcess()
    {
        $mockTmaBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\TransportManagerApplication', $mockTmaBusinessService);

        $mockTmaBusinessService->shouldReceive('process')
            ->once()
            ->with(['data' => ['application' => 213, 'transportManager' => 432, 'action' => 'D']])
            ->andReturn('RESPONSE');

        $response = $this->sut->process(['applicationId' => 213, 'transportManagerId' => 432]);

        $this->assertEquals('RESPONSE', $response);
    }
}

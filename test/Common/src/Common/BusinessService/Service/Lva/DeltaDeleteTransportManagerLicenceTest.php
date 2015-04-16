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
use Common\BusinessService\Service\Lva\DeltaDeleteTransportManagerLicence;
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
        $this->sut = new DeltaDeleteTransportManagerLicence();

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

    public function testProcessMissingTransportManagerLicence()
    {
        $this->setExpectedException('\InvalidArgumentException', 'params key "transportManagerLicenceId" must be set');
        $this->sut->process(['applicationId' => 12]);
    }

    public function testProcess()
    {
        $mockTmlEntityService = m::mock('\Common\Service\Entity\TransportManagerLicenceEntityService');
        $this->sm->setService('Entity\TransportManagerLicence', $mockTmlEntityService);

        $mockTmaBusinessService = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\TransportManagerApplication', $mockTmaBusinessService);

        $mockTmlEntityService->shouldReceive('getTransportManagerLicence')
            ->once()
            ->with(432)
            ->andReturn(['transportManager' => ['id' => 44]]);

        $mockTmaBusinessService->shouldReceive('process')
            ->once()
            ->with(['data' => ['application' => 213, 'transportManager' => 44, 'action' => 'D']])
            ->andReturn('RESPONSE');

        $response = $this->sut->process(['applicationId' => 213, 'transportManagerLicenceId' => 432]);

        $this->assertEquals('RESPONSE', $response);
    }
}

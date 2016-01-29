<?php

/**
 * Licence Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceLvaAdapter;

/**
 * Licence Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceLvaAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new LicenceLvaAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testGetIdentifier()
    {
        $this->controller->shouldReceive('params')
            ->with('licence')
            ->andReturn(6);

        $this->assertEquals(6, $this->sut->getIdentifier());
    }

    public function testGetIdentifierWithoutLicenceParam()
    {
        $mockApplicationAdapter = m::mock();
        $this->sm->setService('ApplicationLvaAdapter', $mockApplicationAdapter);
        $mockApplicationService = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->controller->shouldReceive('params')
            ->with('licence')
            ->andReturn(null);

        $mockApplicationAdapter
            ->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn(87);

        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with(87)
            ->andReturn(6);

        $this->assertEquals(6, $this->sut->getIdentifier());
    }

    public function testAlterForm()
    {
        $mockForm = m::mock('\Zend\Form\Form');

        $mockForm->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('saveAndContinue')
                ->getMock()
            );

        $this->assertNull($this->sut->alterForm($mockForm));
    }
}

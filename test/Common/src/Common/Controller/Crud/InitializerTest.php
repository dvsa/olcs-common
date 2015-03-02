<?php

/**
 * Crud Initializer Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Crud;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Controller\Crud\Initializer;

/**
 * Crud Initializer Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class InitializerTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Initializer();
    }

    public function testInitializeWithoutCrudController()
    {
        $controller = m::mock();
        $sm = Bootstrap::getServiceManager();

        $this->assertNull($this->sut->initialize($controller, $sm));
    }

    public function testInitialize()
    {
        // Params
        $controller = m::mock('\Common\Controller\Interfaces\CrudControllerInterface');
        $sm = Bootstrap::getServiceManager();

        // Mocks
        $mockListener = m::mock();
        $sm->setService('CrudListener', $mockListener);
        $mockEm = m::mock();

        // Expectations
        $mockListener->shouldReceive('setController')
            ->with($controller);

        $controller->shouldReceive('getEventManager')
            ->andReturn($mockEm);

        $mockEm->shouldReceive('attach')
            ->with($mockListener);

        $sm->shouldReceive('getServiceLocator')
            ->andReturnSelf();

        $this->assertNull($this->sut->initialize($controller, $sm));
    }
}

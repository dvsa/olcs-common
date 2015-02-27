<?php

/**
 * Generic Crud Controller Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Crud;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Controller\Crud\GenericCrudControllerFactory;

/**
 * Generic Crud Controller Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericCrudControllerFactoryTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new GenericCrudControllerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        // Params
        $serviceName = null;
        $requestedName = 'Crud\FooController';

        // Mocks
        $mockCrudServiceManager = m::mock();
        $this->sm->setService('CrudServiceManager', $mockCrudServiceManager);
        $mockFoo = m::mock();
        $mockGenericController = m::mock();
        $this->sm->setService('GenericCrudController', $mockGenericController);

        // Expectations
        $this->sm->shouldReceive('getServiceLocator')
            ->andReturnSelf();

        $mockCrudServiceManager->shouldReceive('get')
            ->with('FooCrudService')
            ->andReturn($mockFoo);

        $mockGenericController->shouldReceive('setCrudService')
            ->with($mockFoo)
            ->shouldReceive('setTranslationPrefix')
            ->with('crud-foo');

        $this->assertSame($mockGenericController, $this->sut->createService($this->sm, $serviceName, $requestedName));
    }
}

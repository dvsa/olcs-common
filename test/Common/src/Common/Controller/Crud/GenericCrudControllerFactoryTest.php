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
use Zend\Mvc\MvcEvent;

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
        $this->sm->shouldReceive('getServiceLocator')->andReturnSelf();
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

        // Config / options
        $config = [
            'crud_controller_config' => [
                $requestedName => [
                    'pageLayout' => 'test',
                ]
            ]
        ];
        $this->sm->setService('Config', $config);

        // Event manager
        $em = m::mock('\Zend\EventManager\EventManager');
        $em->shouldReceive('attach')->with(MvcEvent::EVENT_DISPATCH, [$mockGenericController, 'setUpParams'], 100);
        $em->shouldReceive('attach')->with(MvcEvent::EVENT_DISPATCH, [$mockGenericController, 'setUpScripts'], 10000);
        $mockGenericController->shouldReceive('setUpParams')->andReturn(null);
        $mockGenericController->shouldReceive('setUpScripts')->andReturn(null);
        $mockGenericController->shouldReceive('getEventManager')->andReturn($em);

        // Expectations
        $mockCrudServiceManager->shouldReceive('get')
            ->with('FooCrudService')
            ->andReturn($mockFoo);

        $mockGenericController->shouldReceive('setCrudService')->with($mockFoo);
        $mockGenericController->shouldReceive('setTranslationPrefix')->with('crud-foo');
        $mockGenericController->shouldReceive('setOptions')->once()
            ->with($config['crud_controller_config'][$requestedName]);

        $this->assertSame($mockGenericController, $this->sut->createService($this->sm, $serviceName, $requestedName));
    }
}

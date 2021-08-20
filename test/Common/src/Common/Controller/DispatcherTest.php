<?php

namespace CommonTest\Controller;

use Common\Controller\Dispatcher;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use Laminas\Mvc\Application;
use Laminas\Mvc\Controller\AbstractPluginManager;
use Laminas\Mvc\Controller\Plugin\CreateHttpNotFoundModel;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch;
use Laminas\Stdlib\DispatchableInterface;
use Laminas\View\Model\ViewModel;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Mockery\MockInterface;

class DispatcherTest extends MockeryTestCase
{
    /**
     * @test
     */
    public function getDelegate_IsDefined()
    {
        $dispatcher = new Dispatcher(m::mock());
        $this->assertIsCallable([$dispatcher, 'getDelegate']);
    }

    /**
     * @depends getDelegate_IsDefined
     * @test
     */
    public function getDelegate_ReturnsDelegateController()
    {
        $controller = m::mock();
        $dispatcher = new Dispatcher($controller);
        $this->assertSame($controller, $dispatcher->getDelegate());
    }

    /**
     * @test
     */
    public function canBeDispatched()
    {
        // Set Up
        $controller = m::mock();
        $dispatcher = new Dispatcher($controller);

        // Assert
        $this->assertInstanceOf(DispatchableInterface::class, $dispatcher);
    }

    /**
     * @depends canBeDispatched
     * @test
     */
    public function dispatch_CallsControllerAction()
    {
        // Set Up
        $dispatcher = $this->setUpSut(m::mock());
        $event = $this->setUpMvcEvent($expectedAction = 'foo');
        $dispatcher->setEvent($event);

        // Define Expectations
        $dispatcher->getDelegate()->shouldReceive(sprintf('%sAction', $expectedAction))->once();

        // Execute
        $dispatcher->dispatch($event->getRequest(), $event->getResponse());
    }

    /**
     * @depends dispatch_CallsControllerAction
     * @test
     */
    public function dispatch_CallsControllerAction_WithRequestAndRouteMatchAndResponse()
    {
        // Set Up
        $dispatcher = $this->setUpSut(m::mock());
        $event = $this->setUpMvcEvent($expectedAction = 'foo');
        $dispatcher->setEvent($event);
        $request = $event->getRequest();
        $routeMatch = $event->getRouteMatch();
        $response = $event->getResponse();

        // Define Expectations
        $dispatcher->getDelegate()->shouldReceive(sprintf('%sAction', $expectedAction))->once()->with($request, $routeMatch, $response);

        // Execute
        $dispatcher->dispatch($request, $event->getResponse());
    }

    /**
     * @depends canBeDispatched
     * @test
     */
    public function dispatch_DispatchesAnEvent_AndReturnsTheEventResult()
    {
        // Set Up
        $dispatcher = $this->setUpSut();
        $event = $this->setUpMvcEvent();
        $expectedResult = 'EXPECTED_RESULT';
        $event->setResult($expectedResult);
        $dispatcher->setEvent($event);
        $eventManager = $this->setUpEventManager();
        $dispatcher->setEventManager($eventManager);

        // Define Expectations
        $eventManager->shouldReceive('triggerEventUntil')->once()->andReturn(new ResponseCollection());

        // Execute
        $response = $dispatcher->dispatch($event->getRequest(), $event->getResponse());

        // Assert
        $this->assertSame($expectedResult, $response);
    }

    /**
     * @depends canBeDispatched
     * @test
     */
    public function dispatch_DispatchesAnEvent_AndReturnsTheLastResult()
    {
        // Set Up
        $dispatcher = $this->setUpSut();
        $event = $this->setUpMvcEvent();

        $eventManager = $this->setUpEventManager();
        $dispatcher->setEventManager($eventManager);

        $expectedResult = 'EXPECTED_RESULT';
        $responseCollection = new ResponseCollection();
        $responseCollection->push($expectedResult);
        $responseCollection->setStopped(true);

        // Define Expectations
        $eventManager->shouldReceive('triggerEventUntil')->once()->andReturn($responseCollection);

        // Execute
        $response = $dispatcher->dispatch($event->getRequest(), $event->getResponse());

        // Assert
        $this->assertSame($expectedResult, $response);
    }

    /**
     * @return string[][]
     */
    public function controllerActionsThatYieldA404DataProvider(): array
    {
        return [
            'empty controller action' => [''],
            'undefined controller action' => ['someNonCallableMethod'],
        ];
    }

    /**
     * @param string $action
     * @depends canBeDispatched
     * @dataProvider controllerActionsThatYieldA404DataProvider
     * @test
     */
    public function dispatch_WithInvalidAction_Sets404ResponseStatusCode(string $action)
    {
        // Set Up
        $dispatcher = $this->setUpSut();
        $event = $this->setUpMvcEvent($action);
        $dispatcher->setEvent($event);

        // Execute
        $dispatcher->dispatch($event->getRequest(), $event->getResponse());

        // Assert
        $response = $event->getResponse();
        assert($response instanceof Response, 'Expected instance of Response');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @param string $action
     * @depends canBeDispatched
     * @dataProvider controllerActionsThatYieldA404DataProvider
     * @test
     */
    public function dispatch_WithInvalidAction_ReturnsResultOfCreateHttpNotFoundModelPlugin(string $action)
    {
        // Set Up
        $dispatcher = $this->setUpSut();
        $event = $this->setUpMvcEvent($action);
        $dispatcher->setEvent($event);
        $pluginManager = $this->setUpPluginManager();
        $dispatcher->setPluginManager($pluginManager);
        $notFoundModelCreationPlugin = $pluginManager->get('createHttpNotFoundModel');
        $expectedResult = new ViewModel();

        // Define Expectations
        $notFoundModelCreationPlugin->shouldReceive('__invoke')->once()->andReturn($expectedResult);

        // Execute
        $response = $dispatcher->dispatch($event->getRequest(), $event->getResponse());

        // Assert
        $this->assertSame($expectedResult, $response);
    }

    /**
     * @param string $action
     * @depends canBeDispatched
     * @dataProvider controllerActionsThatYieldA404DataProvider
     * @test
     */
    public function dispatch_WithInvalidAction_SetsEventError(string $action)
    {
        // Set Up
        $dispatcher = $this->setUpSut();
        $event = $this->setUpMvcEvent($action);
        $dispatcher->setEvent($event);
        $pluginManager = $this->setUpPluginManager();
        $dispatcher->setPluginManager($pluginManager);
        $notFoundModelCreationPlugin = $pluginManager->get('createHttpNotFoundModel');
        $expectedResult = new ViewModel();

        // Define Expectations
        $notFoundModelCreationPlugin->shouldReceive('__invoke')->once()->andReturn($expectedResult);

        // Execute
        $dispatcher->dispatch($event->getRequest(), $event->getResponse());

        // Assert
        $this->assertEquals(Application::ERROR_CONTROLLER_CANNOT_DISPATCH, $event->getError());
    }

    /**
     * @return MockInterface
     */
    protected function setUpEventManager(): MockInterface
    {
        $responseCollection = new ResponseCollection();
        $eventManager = m::mock(EventManagerInterface::class);
        $eventManager->shouldIgnoreMissing();
        $eventManager->shouldReceive('triggerEventUntil')->andReturn($responseCollection)->byDefault();
        return $eventManager;
    }

    /**
     * @return object
     */
    protected function setUpController(): object
    {
        return new class() {

        };
    }

    /**
     * @return PluginManager
     */
    protected function setUpPluginManager(): PluginManager
    {
        $pluginManager = new PluginManager();

        $notFoundModelCreationPlugin = m::mock(CreateHttpNotFoundModel::class);
        $notFoundModelCreationPlugin->shouldIgnoreMissing();

        assert($pluginManager instanceof AbstractPluginManager, 'Expected instance of AbstractPluginManager');
        $pluginManager->setService('createHttpNotFoundModel', $notFoundModelCreationPlugin);

        return $pluginManager;
    }

    /**
     * @param object|null $controller
     * @return Dispatcher
     */
    protected function setUpSut(object $controller = null): Dispatcher
    {
        if (null === $controller) {
            $controller = $this->setUpController();
        }
        $dispatcher = new Dispatcher($controller);
        $dispatcher->setEvent($this->setUpMvcEvent());
        return $dispatcher;
    }

    /**
     * @param string $defaultAction
     * @return MvcEvent
     */
    protected function setUpMvcEvent(string $defaultAction = 'foo'): MvcEvent
    {
        $event = new MvcEvent();

        $routeMatch = m::mock(RouteMatch::class);
        $routeMatch->shouldIgnoreMissing();
        $routeMatch->shouldReceive('getParam')->with('action')->andReturn($defaultAction)->byDefault();
        $event->setRouteMatch($routeMatch);

        $request = new Request();
        $event->setRequest($request);

        $response = new Response();
        $event->setResponse($response);

        return $event;
    }
}

<?php

namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Interfaces\ControllerAwareInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Controller\Lva\Delegators\AbstractAdapterDelegator
 */
class AdapterDelegatorTestAbstract extends MockeryTestCase
{
    protected $delegator = 'changeMe';

    protected $adapter = 'changeMe';

    public function testInvoke()
    {
        $controller = m::mock(AbstractActionController::class);

        $adapter = m::mock(ControllerAwareInterface::class);
        $adapter->shouldReceive('setController')->with($controller)->once();

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('getServiceLocator->get')->with($this->adapter)->once()->andReturn($adapter);

        $controller->shouldReceive('setAdapter')->with($adapter)->once();

        $requestedName = 'foo';
        $callback = function () use ($controller) {
            return $controller;
        };

        $sut = new $this->delegator();
        $return = $sut($sm, $requestedName, $callback);

        $this->assertSame($controller, $return);
    }

    /**
     * @todo OLCS-28149
     */
    public function testCreatedDelegatorWithName()
    {
        $controller = m::mock(AbstractActionController::class);

        $adapter = m::mock(ControllerAwareInterface::class);
        $adapter->shouldReceive('setController')->with($controller)->once();

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('getServiceLocator->get')->with($this->adapter)->once()->andReturn($adapter);

        $controller->shouldReceive('setAdapter')->with($adapter)->once();

        $name = 'foo';
        $requestedName = 'foo';
        $callback = function () use ($controller) {
            return $controller;
        };

        $sut = new $this->delegator();
        $return = $sut->createDelegatorWithName($sm, $name, $requestedName, $callback);

        $this->assertSame($controller, $return);
    }
}

<?php

/**
 * Application Type Of Licence Delegator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\ApplicationTypeOfLicenceDelegator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Application Type Of Licence Delegator Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceDelegatorTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ApplicationTypeOfLicenceDelegator();
    }

    /**
     * @group lva_delegators
     */
    public function testCreateDelegatorWithName()
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockAdapter = m::mock();
        $name = 'foo';
        $requestedName = 'bar';
        $mockController = m::mock('\Zend\Mvc\Controller\AbstractController');
        $callback = function () use ($mockController) {
            return $mockController;
        };

        $mockController->shouldReceive('setAdapter')
            ->with($mockAdapter);

        $sm->shouldReceive('getServiceLocator')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('ApplicationTypeOfLicenceAdapter')
            ->andReturn($mockAdapter);

        $this->assertSame($mockController, $this->sut->createDelegatorWithName($sm, $name, $requestedName, $callback));
    }

    /**
     * @group lva_delegators
     */
    public function testCreateDelegatorWithNameWithControllerAware()
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockAdapter = m::mock('\Common\Controller\Lva\Interfaces\ControllerAwareInterface');
        $name = 'foo';
        $requestedName = 'bar';
        $mockController = m::mock('\Zend\Mvc\Controller\AbstractController');
        $callback = function () use ($mockController) {
            return $mockController;
        };

        $mockController->shouldReceive('setAdapter')
            ->with($mockAdapter);

        $mockAdapter->shouldReceive('setController')
            ->with($mockController);

        $sm->shouldReceive('getServiceLocator')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('ApplicationTypeOfLicenceAdapter')
            ->andReturn($mockAdapter);

        $this->assertSame($mockController, $this->sut->createDelegatorWithName($sm, $name, $requestedName, $callback));
    }
}

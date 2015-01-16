<?php

/**
 * Application Vehicle Goods Delegator Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Delegators;

use Common\Controller\Lva\Delegators\ApplicationVehicleGoodsDelegator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Application Vehicle Goods Delegator Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationVehicleGoodsDelegatorTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ApplicationVehicleGoodsDelegator();
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
            ->with('ApplicationVehicleGoodsAdapter')
            ->andReturn($mockAdapter);

        $this->assertSame($mockController, $this->sut->createDelegatorWithName($sm, $name, $requestedName, $callback));
    }
}

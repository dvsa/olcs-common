<?php

/**
 * Test Abstract Vehicles Goods Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;

/**
 * Test Abstract Vehicles Goods Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractVehiclesGoodsControllerTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $adapter;

    public function setUp()
    {
        $this->sut = m::mock('\Common\Controller\Lva\AbstractVehiclesGoodsController')->makePartial();

        $this->sm = Bootstrap::getServiceManager();
        $this->adapter = m::mock('\Common\Controller\Lva\Interfaces\AdapterInterface');

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setAdapter($this->adapter);
    }

    public function testIndexAction()
    {
        $this->assertTrue(true);
    }
}

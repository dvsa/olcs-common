<?php

/**
 * Variation Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationLvaAdapter;

/**
 * Variation Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLvaAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new VariationLvaAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testGetIdentifier()
    {
        $applicationAdapter = m::mock();

        $this->sm->setService('ApplicationLvaAdapter', $applicationAdapter);

        $applicationAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn(5);

        $this->assertEquals(5, $this->sut->getIdentifier());
    }
}

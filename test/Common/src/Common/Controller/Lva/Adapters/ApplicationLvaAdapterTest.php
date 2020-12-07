<?php

/**
 * Application Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationLvaAdapter;

/**
 * Application Lva Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationLvaAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp(): void
    {
        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Laminas\Mvc\Controller\AbstractController');

        $this->sut = new ApplicationLvaAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testAlterForm()
    {
        // This method should do nothing
        // So we don't really need expectations or assertions
        $mockForm = m::mock('\Laminas\Form\Form');
        $this->assertNull($this->sut->alterForm($mockForm));
    }

    public function testGetIdentifierThrowsException()
    {
        $this->expectException('\Exception', 'Can\'t get the application id from this controller');

        $id = null;

        $this->controller->shouldReceive('params')
            ->with('application')
            ->andReturn($id);

        $this->sut->getIdentifier();
    }

    public function testGetIdentifier()
    {
        $this->controller->shouldReceive('params')
            ->with('application')
            ->andReturn(6);

        $this->assertEquals(6, $this->sut->getIdentifier());
    }
}

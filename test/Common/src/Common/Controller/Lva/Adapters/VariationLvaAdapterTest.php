<?php

namespace CommonTest\Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractLvaAdapter;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationLvaAdapter;

class VariationLvaAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $container;
    protected $controller;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);

        $this->controller = m::mock('\Laminas\Mvc\Controller\AbstractController');

        $this->sut = new VariationLvaAdapter($this->container);
        $this->sut->setController($this->controller);
    }

    public function testGetIdentifier()
    {
        $applicationAdapter = m::mock(AbstractLvaAdapter::class);

        $this->container->expects('get')->with('ApplicationLvaAdapter')->andReturn($applicationAdapter);

        $applicationAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn(5);

        $this->assertEquals(5, $this->sut->getIdentifier());
    }
}

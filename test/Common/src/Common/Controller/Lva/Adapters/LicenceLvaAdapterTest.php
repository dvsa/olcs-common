<?php

namespace CommonTest\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\AbstractController;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceLvaAdapter;

class LicenceLvaAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $container;
    protected $controller;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);

        $this->controller = m::mock(AbstractController::class);

        $this->sut = new LicenceLvaAdapter($this->container);
        $this->sut->setController($this->controller);
    }

    public function testAlterForm()
    {
        $mockForm = m::mock('\Laminas\Form\Form');

        $mockForm->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('remove')
                ->with('saveAndContinue')
                ->getMock()
            );

        $this->assertNull($this->sut->alterForm($mockForm));
    }
}

<?php

namespace CommonTest\Common\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\GenericBusinessTypeAdapter;

class GenericBusinessTypeAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected $container;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->sut = new GenericBusinessTypeAdapter($this->container);
    }

    public function testAlterFormIsNoOp()
    {
        $this->assertNull($this->sut->alterFormForOrganisation(m::mock('Laminas\Form\Form'), 123));
    }
}

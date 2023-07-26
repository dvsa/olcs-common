<?php

namespace OlcsTest\Controller\Lva\Adapters;

use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationPeopleAdapter;

class VariationPeopleAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $container;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);

        $this->sut = new VariationPeopleAdapter($this->container);
    }

    public function testCanModify()
    {
        $this->assertTrue($this->sut->canModify(123));
    }
}

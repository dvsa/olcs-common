<?php

namespace CommonTest\Util;

use Common\Util\AbstractServiceFactory;
use CommonTest\Common\Util\Stub\ServiceWithFactoryStub;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Util\AbstractServiceFactory
 */
class AbstractServiceFactoryTest extends MockeryTestCase
{
    /** @var  AbstractServiceFactory | m\MockInterface */
    protected $sut;

    /** @var  m\MockInterface | ContainerInterface */
    protected $mockSm;

    public function setUp(): void
    {
        $this->sut = m::mock(AbstractServiceFactory::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockSm = m::mock(ContainerInterface::class);
    }

    /**
     * @dataProvider dpTestCanCreate
     */
    public function testCanCreate($requestedName, $expect)
    {
        static::assertEquals($expect, $this->sut->canCreate($this->mockSm, $requestedName));
    }

    public function dpTestCanCreate()
    {
        //  use real classes to test
        return [
            [
                //  @see \Common\Service\Helper\FormHelperService
                'fqcn' => 'Helper\Form',
                'expect' => true,
            ],
            [
                'fqcn' => 'Wrong\Wrong',
                'expect' => false,
            ],
        ];
    }

    public function testInvokeUsingFactory()
    {
        $className = 'unit_className';

        $this->sut->shouldReceive('getClassName')->with($className)->andReturn(ServiceWithFactoryStub::class);

        $actual = ($this->sut)($this->mockSm, $className);

        static::assertInstanceOf(FactoryInterface::class, $actual);
        static::assertInstanceOf(ServiceWithFactoryStub::class, $actual);
    }

    public function testInvoke()
    {
        $className = 'unit_className';

        $this->sut->shouldReceive('getClassName')->with($className)->andReturn('\stdClass');

        $actual = ($this->sut)($this->mockSm, $className);

        static::assertInstanceOf('\stdClass', $actual);
    }
}

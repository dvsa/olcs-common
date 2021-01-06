<?php

namespace CommonTest\Util;

use Common\Util\AbstractServiceFactory;
use CommonTest\Util\Stub\ServiceWithFactoryStub;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Util\AbstractServiceFactory
 */
class AbstractServiceFactoryTest extends MockeryTestCase
{
    /** @var  AbstractServiceFactory | m\MockInterface */
    protected $sut;

    /** @var  m\MockInterface | ServiceLocatorInterface */
    protected $mockSm;

    public function setUp(): void
    {
        $this->sut = m::mock(AbstractServiceFactory::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockSm = m::mock(ServiceLocatorInterface::class);
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

    /**
     * @dataProvider dpTestCanCreate
     * @todo OLCS-28149
     */
    public function testCanCreateServiceWithName($fqcn, $expect)
    {
        static::assertEquals($expect, $this->sut->canCreateServiceWithName($this->mockSm, null, $fqcn));
    }

    /**
     * @todo OLCS-28149
     */
    public function testCreateServiceWithNameUsingFactory()
    {
        $className = 'unit_className';

        $this->sut->shouldReceive('getClassName')->with($className)->andReturn(ServiceWithFactoryStub::class);

        $actual = $this->sut->createServiceWithName($this->mockSm, null, $className);

        static::assertInstanceOf(FactoryInterface::class, $actual);
        static::assertInstanceOf(ServiceWithFactoryStub::class, $actual);
    }

    /**
     * @todo OLCS-28149
     */
    public function testCreateServiceWithName()
    {
        $className = 'unit_className';

        $this->sut->shouldReceive('getClassName')->with($className)->andReturn('\stdClass');

        $actual = $this->sut->createServiceWithName($this->mockSm, null, $className);

        static::assertInstanceOf('\stdClass', $actual);
    }
}

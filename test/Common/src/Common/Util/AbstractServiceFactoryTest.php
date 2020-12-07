<?php

namespace CommonTest\Util;

use Common\Util\AbstractServiceFactory;
use CommonTest\Util\Stub\ServiceWithFactoryStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\FactoryInterface;

/**
 * @covers \Common\Util\AbstractServiceFactory
 */
class AbstractServiceFactoryTest extends MockeryTestCase
{
    /** @var  AbstractServiceFactory | m\MockInterface */
    protected $sut;
    /** @var  m\MockInterface | \Laminas\ServiceManager\ServiceLocatorInterface */
    protected $mockSm;

    public function setUp(): void
    {
        $this->sut = m::mock(AbstractServiceFactory::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockSm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
    }

    /**
     * @dataProvider dpTestCanCreateServiceWithName
     */
    public function testCanCreateServiceWithName($fqcn, $expect)
    {
        static::assertEquals($expect, $this->sut->canCreateServiceWithName($this->mockSm, null, $fqcn));
    }

    public function dpTestCanCreateServiceWithName()
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

    public function testCreateServiceWithNameUsingFactory()
    {
        $className = 'unit_className';

        $this->sut->shouldReceive('getClassName')->with($className)->andReturn(ServiceWithFactoryStub::class);

        $actual = $this->sut->createServiceWithName($this->mockSm, null, $className);

        static::assertInstanceOf(FactoryInterface::class, $actual);
        static::assertInstanceOf(ServiceWithFactoryStub::class, $actual);
    }

    public function testCreateServiceWithName()
    {
        $className = 'unit_className';

        $this->sut->shouldReceive('getClassName')->with($className)->andReturn('\stdClass');

        $actual = $this->sut->createServiceWithName($this->mockSm, null, $className);

        static::assertInstanceOf('\stdClass', $actual);
    }
}

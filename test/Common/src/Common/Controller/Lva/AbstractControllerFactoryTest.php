<?php

namespace CommonTest\Controller\Lva;

use Common\Controller\Lva\AbstractControllerFactory;
use CommonTest\Controller\Lva\Stubs\ControllerWithFactoryStub;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Controller\Lva\AbstractControllerFactory
 */
class AbstractControllerFactoryTest extends MockeryTestCase
{
    /** @var  AbstractControllerFactory */
    protected $sut;

    /** @var  m\MockInterface | ControllerManager */
    protected $mockScm;

    /** @var  m\MockInterface | ServiceLocatorInterface */
    protected $mockSm;

    protected function setUp(): void
    {
        $this->sut = new AbstractControllerFactory();

        $this->mockSm = m::mock(ServiceLocatorInterface::class);

        $this->mockScm = m::mock(ControllerManager::class);
        $this->mockScm->shouldReceive('getServiceLocator')->andReturn($this->mockSm);
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCanCreate()
    {
        $name = 'bar';
        $requestedName = 'foo';

        $config = array(
            'controllers' => array(
                'lva_controllers' => array(
                    'foo' => 'bar'
                )
            )
        );

        $this->mockSm->shouldReceive('get')->with('Config')->andReturn($config);

        $this->assertTrue($this->sut->canCreate($this->mockScm, $requestedName));

        // TODO OLCS-28149
        $this->assertTrue($this->sut->canCreateServiceWithName($this->mockScm, $name, $requestedName));
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCanCreateWithoutConfigMatch()
    {
        $name = 'foo';
        $requestedName = 'bar';

        $config = array(
            'controllers' => array(
                'lva_controllers' => array(
                    'blap' => 'bar'
                )
            )
        );

        $this->mockSm->shouldReceive('get')->with('Config')->andReturn($config);

        $this->assertFalse($this->sut->canCreate($this->mockScm, $requestedName));

        // TODO OLCS-28149
        $this->assertFalse($this->sut->canCreateServiceWithName($this->mockScm, $name, $requestedName));
    }

    /**
     * @group lva_abstract_factory
     */
    public function testInvoke()
    {
        $name = 'bar';
        $requestedName = 'foo';

        $config = array(
            'controllers' => array(
                'lva_controllers' => array(
                    'foo' => '\stdClass'
                )
            )
        );

        $this->mockSm->shouldReceive('get')->with('Config')->andReturn($config);

        $this->assertInstanceOf('\stdClass', ($this->sut)($this->mockScm, $requestedName));

        // TODO OLCS-28149
        $this->assertInstanceOf('\stdClass', $this->sut->createServiceWithName($this->mockScm, $name, $requestedName));
    }

    public function testInvokeUsingFactory()
    {
        $name = 'unit_Name';
        $requestedName = 'unit_reqName';

        $config = [
            'controllers' => [
                'lva_controllers' => [
                    $requestedName => ControllerWithFactoryStub::class,
                ],
            ],
        ];

        $this->mockSm->shouldReceive('get')->with('Config')->andReturn($config);

        $actual = ($this->sut)($this->mockScm, $requestedName);
        static::assertInstanceOf(FactoryInterface::class, $actual);
        static::assertInstanceOf(ControllerWithFactoryStub::class, $actual);

        // TODO OLCS-28149
        $actual = $this->sut->createServiceWithName($this->mockScm, $name, $requestedName);
        static::assertInstanceOf(FactoryInterface::class, $actual);
        static::assertInstanceOf(ControllerWithFactoryStub::class, $actual);
    }
}

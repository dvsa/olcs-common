<?php

namespace CommonTest\Controller\Lva;

use Common\Controller\Lva\AbstractControllerFactory;
use CommonTest\Controller\Lva\Stubs\ControllerWithFactoryStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\FactoryInterface;

/**
 * @covers \Common\Controller\Lva\AbstractControllerFactory
 */
class AbstractControllerFactoryTest extends MockeryTestCase
{
    /** @var  AbstractControllerFactory */
    protected $sut;
    /** @var  m\MockInterface | \Zend\Mvc\Controller\ControllerManager */
    protected $mockScm;
    /** @var  m\MockInterface | \Zend\ServiceManager\ServiceLocatorInterface */
    protected $mockSm;

    protected function setUp(): void
    {
        $this->sut = new AbstractControllerFactory();

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);

        $this->mockScm = m::mock(\Zend\Mvc\Controller\ControllerManager::class);
        $this->mockScm->shouldReceive('getServiceLocator')->andReturn($this->mockSm);
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCanCreateServiceWithName()
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

        $this->assertTrue($this->sut->canCreateServiceWithName($this->mockScm, $name, $requestedName));
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCanCreateServiceWithNameWithoutConfigMatch()
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

        $this->assertFalse($this->sut->canCreateServiceWithName($this->mockScm, $name, $requestedName));
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCreateServiceWithName()
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

        $this->assertInstanceOf('\stdClass', $this->sut->createServiceWithName($this->mockScm, $name, $requestedName));
    }

    public function testCreateServiceWithNameUsingFactory()
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

        $actual = $this->sut->createServiceWithName($this->mockScm, $name, $requestedName);

        static::assertInstanceOf(FactoryInterface::class, $actual);
        static::assertInstanceOf(ControllerWithFactoryStub::class, $actual);
    }
}

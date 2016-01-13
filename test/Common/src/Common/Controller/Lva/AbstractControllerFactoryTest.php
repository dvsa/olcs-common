<?php

/**
 * Abstract Controller Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Controller\Lva\AbstractControllerFactory;

/**
 * Abstract Controller Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractControllerFactoryTest extends MockeryTestCase
{
    protected $sut;

    protected function setUp()
    {
        $this->sut = new AbstractControllerFactory();
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCanCreateServiceWithName()
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $name = 'bar';
        $requestedName = 'foo';

        $config = array(
            'controllers' => array(
                'lva_controllers' => array(
                    'foo' => 'bar'
                )
            )
        );

        $sm->shouldReceive('getServiceLocator->get')
            ->with('Config')
            ->andReturn($config);

        $this->assertTrue($this->sut->canCreateServiceWithName($sm, $name, $requestedName));
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCanCreateServiceWithNameWithoutConfigMatch()
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $name = 'foo';
        $requestedName = 'bar';

        $config = array(
            'controllers' => array(
                'lva_controllers' => array(
                    'blap' => 'bar'
                )
            )
        );

        $sm->shouldReceive('getServiceLocator->get')
            ->with('Config')
            ->andReturn($config);

        $this->assertFalse($this->sut->canCreateServiceWithName($sm, $name, $requestedName));
    }

    /**
     * @group lva_abstract_factory
     */
    public function testCreateServiceWithName()
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $name = 'bar';
        $requestedName = 'foo';

        $config = array(
            'controllers' => array(
                'lva_controllers' => array(
                    'foo' => '\stdClass'
                )
            )
        );

        $sm->shouldReceive('getServiceLocator->get')
            ->with('Config')
            ->andReturn($config);

        $this->assertInstanceOf('\stdClass', $this->sut->createServiceWithName($sm, $name, $requestedName));
    }
}

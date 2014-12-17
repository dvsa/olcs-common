<?php

/**
 * Abstract Entity Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use CommonTest\Bootstrap;
use CommonTest\Traits\MockDateTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Entity Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractEntityServiceTestCase extends MockeryTestCase
{
    use MockDateTrait;

    protected $sut;

    protected $sm;

    protected $restHelper;

    protected $restCallOrder = 0;

    protected $setProperty;

    protected function setUp()
    {
        $this->restHelper = $this->getMock('\stdClass', array('makeRestCall'));
        $this->restCallOrder = 0;

        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);
        $this->sm->setService('Helper\Rest', $this->restHelper);

        $this->sut->setServiceLocator($this->sm);
    }

    protected function expectOneRestCall($entity, $method, $data, $bundle = null)
    {
        $expectation = $this->restHelper->expects($this->once())->method('makeRestCall');

        if ($bundle !== null) {
            return $expectation->with($entity, $method, $data, $bundle);
        }

        return $expectation->with($entity, $method, $data);
    }

    protected function expectedRestCallInOrder($entity, $method, $data, $bundle = null)
    {
        $expectation = $this->restHelper->expects($this->at($this->restCallOrder))->method('makeRestCall');
        $this->restCallOrder++;

        if ($bundle !== null) {
            return $expectation->with($entity, $method, $data, $bundle);
        }

        return $expectation->with($entity, $method, $data);
    }

    /**
     * Bind a closure the the SUT which allows us to set the value of a protected property
     *
     * @param string $entity
     */
    protected function setEntity($entity)
    {
        $this->setProperty('entity', $entity);
    }

    /**
     * Wrap the closure invokable
     *
     * @param string $name
     * @param mixed $value
     */
    protected function setProperty($name, $value)
    {
        $this->setProperty->__invoke($name, $value);
    }

    public function getMockForAbstractClass(
        $originalClassName,
        array $arguments = array(),
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true,
        $mockedMethods = array(),
        $cloneArguments = false
    ) {
        $mock = parent::getMockForAbstractClass(
            $originalClassName,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $mockedMethods,
            $cloneArguments
        );

        $setProperty = function ($property, $value) {
            $this->$property = $value;
        };

        $this->setProperty = \Closure::bind($setProperty, $mock, $originalClassName);

        return $mock;
    }
}

<?php

namespace CommonTest\Service;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\FormAnnotationBuilderFactory;

/**
 * Class FormAnnotationBuilderFactoryTest
 * @package CommonTest\Service
 */
class FormAnnotationBuilderFactoryTest extends MockeryTestCase
{
    /**
     *
     */
    public function testCreateService()
    {
        $mockFormFactory = m::mock('Zend\Form\FormElementManager');
        $mockValidatorManager = m::mock('Zend\Validator\ValidatorPluginManager');
        $mockFilterManager = m::mock('Zend\Filter\FilterPluginManager');

        $mockServiceLocator = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockServiceLocator->shouldReceive('get')->with('FormElementManager')->andReturn($mockFormFactory);
        $mockServiceLocator->shouldReceive('get')->with('ValidatorManager')->andReturn($mockValidatorManager);
        $mockServiceLocator->shouldReceive('get')->with('FilterManager')->andReturn($mockFilterManager);

        $sut = new FormAnnotationBuilderFactory();
        $sut->createService($mockServiceLocator);
    }
}

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
        $mockFormFactory = m::mock('Laminas\Form\FormElementManager');
        $mockValidatorManager = m::mock('Laminas\Validator\ValidatorPluginManager');
        $mockFilterManager = m::mock('Laminas\Filter\FilterPluginManager');

        $mockServiceLocator = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockServiceLocator->shouldReceive('get')->with('FormElementManager')->andReturn($mockFormFactory);
        $mockServiceLocator->shouldReceive('get')->with('ValidatorManager')->andReturn($mockValidatorManager);
        $mockServiceLocator->shouldReceive('get')->with('FilterManager')->andReturn($mockFilterManager);

        $sut = new FormAnnotationBuilderFactory();
        $sut->createService($mockServiceLocator);
    }
}

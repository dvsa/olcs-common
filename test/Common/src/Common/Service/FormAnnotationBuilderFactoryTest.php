<?php

namespace CommonTest\Service;

use Interop\Container\ContainerInterface;
use Laminas\Form\Annotation\AnnotationBuilder;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\FormAnnotationBuilderFactory;

/**
 * Class FormAnnotationBuilderFactoryTest
 * @package CommonTest\Service
 */
class FormAnnotationBuilderFactoryTest extends MockeryTestCase
{
    public function testInvoke(): void
    {
        $mockFormFactory = m::mock('Laminas\Form\FormElementManager');
        $mockValidatorManager = m::mock('Laminas\Validator\ValidatorPluginManager');
        $mockFilterManager = m::mock('Laminas\Filter\FilterPluginManager');

        $mockServiceLocator = m::mock(ContainerInterface::class);
        $mockServiceLocator->shouldReceive('get')->with('FormElementManager')->andReturn($mockFormFactory);
        $mockServiceLocator->shouldReceive('get')->with('ValidatorManager')->andReturn($mockValidatorManager);
        $mockServiceLocator->shouldReceive('get')->with('FilterManager')->andReturn($mockFilterManager);

        $sut = new FormAnnotationBuilderFactory();
        $sut->__invoke($mockServiceLocator, AnnotationBuilder::class);
    }
}

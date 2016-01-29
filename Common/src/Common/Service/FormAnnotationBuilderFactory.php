<?php

namespace Common\Service;

use Common\Form\Annotation\CustomAnnotationBuilder;
use Zend\Form\Factory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FormAnnotationBuilderFactory
 * @package Common\Service
 */
class FormAnnotationBuilderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        //setup a form factory which can use custom form elements
        $formElementManager = $serviceLocator->get('FormElementManager');
        $formFactory = new Factory($formElementManager);

        //setup input filter factory to use custom validators + filters
        $inputFilterFactory = $formFactory->getInputFilterFactory();

        $inputFilterFactory->getDefaultValidatorChain()
            ->setPluginManager($serviceLocator->get('ValidatorManager'));

        $inputFilterFactory->getDefaultFilterChain()
            ->setPluginManager($serviceLocator->get('FilterManager'));

        //create service and set custom form factory
        $annotationBuilder = new CustomAnnotationBuilder();
        $annotationBuilder->setFormFactory($formFactory);
        return $annotationBuilder;
    }
}

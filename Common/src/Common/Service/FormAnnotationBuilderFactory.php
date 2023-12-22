<?php

namespace Common\Service;

use Common\Form\Annotation\CustomAnnotationBuilder;
use Laminas\Form\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Laminas\Form\Factory;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class FormAnnotationBuilderFactory
 * @package Common\Service
 */
class FormAnnotationBuilderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // set up a form factory which can use custom form elements
        $formElementManager = $container->get('FormElementManager');
        $formFactory = new Factory($formElementManager);

        // set up input filter factory to use custom validators + filters
        $inputFilterFactory = $formFactory->getInputFilterFactory();

        $inputFilterFactory->getDefaultValidatorChain()
            ->setPluginManager($container->get('ValidatorManager'));

        $inputFilterFactory->getDefaultFilterChain()
            ->setPluginManager($container->get('FilterManager'));

        // create service and set custom form factory
        $annotationBuilder = new AnnotationBuilder();
        $annotationBuilder->setFormFactory($formFactory);

        return $annotationBuilder;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CustomAnnotationBuilder
    {
        return $this->__invoke($serviceLocator, CustomAnnotationBuilder::class);
    }
}

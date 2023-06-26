<?php

declare(strict_types=1);

namespace Common\Form\View\Helper;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Interop\Container\ContainerInterface;

/**
 * @see FormElement
 * @see \CommonTest\Form\View\Helper\FormElementFactoryTest
 */
class FormElementFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FormElement
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $serviceLocator): FormElement
    {
        return $this->__invoke($serviceLocator, null);
    }

    /**
     * @param ContainerInterface $container
     * @param mixed $requestedName
     * @param array|null $options
     * @return FormElement
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormElement
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $instance = new FormElement();
        $config = $container->get('config');
        $map = $config['form']['element']['renderers'] ?? [];
        foreach ($map as $class => $rendererClass) {
            $instance->addClass($class, $rendererClass);
        }

        return $instance;
    }
}

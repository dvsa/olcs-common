<?php

namespace Common\Form\Element;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class DynamicMultiCheckboxFactory
 * @package Common\Form\Element
 */
class DynamicMultiCheckboxFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DynamicMultiCheckbox
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $dataServiceManager = $container->get('DataServiceManager');
        return new DynamicMultiCheckbox($dataServiceManager);
    }

    /**
     * Create service
     * @deprecated
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        return $this->__invoke($formElementManager, DynamicMultiCheckbox::class);
    }
}

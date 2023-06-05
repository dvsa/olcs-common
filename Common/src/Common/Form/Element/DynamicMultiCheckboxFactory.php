<?php

namespace Common\Form\Element;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\Service\Data\RefData as RefDataService;

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
        $instance = new DynamicMultiCheckbox();
        $instance->setServiceLocator($container->get('DataServiceManager'));

        return $instance;
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

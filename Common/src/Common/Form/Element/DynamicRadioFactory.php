<?php

namespace Common\Form\Element;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class DynamicRadioFactory
 * @package Common\Form\Element
 */
class DynamicRadioFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DynamicRadio
    {
        $instance = new DynamicRadio();
        $instance->setServiceLocator($container->getServiceLocator()->get('DataServiceManager'));

        return $instance;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $formElementManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        return $this->__invoke($formElementManager, DynamicRadio::class);
    }
}

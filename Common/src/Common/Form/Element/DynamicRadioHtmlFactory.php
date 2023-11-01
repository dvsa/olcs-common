<?php

namespace Common\Form\Element;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class DynamicRadioFactory
 * @package Common\Form\Element
 */
class DynamicRadioHtmlFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DynamicRadioHtml
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $dataServiceManager = $container->get('DataServiceManager');
        return new DynamicRadioHtml($dataServiceManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $formElementManager
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $formElementManager)
    {
        return $this->__invoke($formElementManager, DynamicRadioHtml::class);
    }
}

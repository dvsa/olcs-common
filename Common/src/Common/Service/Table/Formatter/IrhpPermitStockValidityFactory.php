<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IrhpPermitStockValidityFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return IrhpPermitStockValidity
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $dateFormatter = $formatterPluginManager->get(Date::class);
        return new IrhpPermitStockValidity($dateFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpPermitStockType
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IrhpPermitStockValidity
    {
        return $this->__invoke($serviceLocator, IrhpPermitStockValidity::class);
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IrhpPermitTypeWithValidityDateFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return IrhpPermitTypeWithValidityDate
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $dateFormatter = $formatterPluginManager->get(Date::class);
        $translator = $container->get('translator');
        return new IrhpPermitTypeWithValidityDate($dateFormatter, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpPermitTypeWithValidityDate
     */
    public function createService(ServiceLocatorInterface $serviceLocator): IrhpPermitTypeWithValidityDate
    {
        return $this->__invoke($serviceLocator, IrhpPermitTypeWithValidityDate::class);
    }
}

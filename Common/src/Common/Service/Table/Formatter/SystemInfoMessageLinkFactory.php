<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SystemInfoMessageLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SystemInfoMessageLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new SystemInfoMessageLink($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SystemInfoMessageLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SystemInfoMessageLink
    {
        return $this->__invoke($serviceLocator, SystemInfoMessageLink::class);
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DashboardTmActionLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return DashboardTmActionLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $viewHelperManager = $container->get('ViewHelperManager');
        $translator = $container->get('translator');
        return new DashboardTmActionLink($urlHelper, $viewHelperManager, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DashboardTmActionLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DashboardTmActionLink
    {
        return $this->__invoke($serviceLocator, DashboardTmActionLink::class);
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DashboardTmApplicationStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return DashboardTmApplicationStatus
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $viewHelperManager = $container->get('viewHelperManager');
        return new DashboardTmApplicationStatus($viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DashboardTmApplicationStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DashboardTmApplicationStatus
    {
        return $this->__invoke($serviceLocator, DashboardTmApplicationStatus::class);
    }
}

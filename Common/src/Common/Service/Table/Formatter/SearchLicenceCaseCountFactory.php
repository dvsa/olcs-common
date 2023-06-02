<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class SearchLicenceCaseCountFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchLicenceCaseCount
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchLicenceCaseCount
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $authService = $container->get(AuthorizationService::class);
        return new SearchLicenceCaseCount($authService);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchLicenceCaseCount
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchLicenceCaseCount
    {
        return $this->__invoke($serviceLocator, SearchLicenceCaseCount::class);
    }
}

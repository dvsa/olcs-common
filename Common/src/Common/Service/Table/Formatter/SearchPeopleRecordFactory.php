<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class SearchPeopleRecordFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchPeopleRecord
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchPeopleRecord
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $authService = $container->get(AuthorizationService::class);
        $urlHelper = $container->get('Helper\Url');
        return new SearchPeopleRecord($authService, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchPeopleRecord
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchPeopleRecord
    {
        return $this->__invoke($serviceLocator, SearchPeopleRecord::class);
    }
}

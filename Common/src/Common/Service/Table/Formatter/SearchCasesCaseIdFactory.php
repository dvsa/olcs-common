<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class SearchCasesCaseIdFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchCasesCaseId
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchCasesCaseId
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $authService = $container->get(AuthorizationService::class);
        return new SearchCasesCaseId($authService);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchCasesCaseId
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchCasesCaseId
    {
        return $this->__invoke($serviceLocator, SearchCasesCaseId::class);
    }
}

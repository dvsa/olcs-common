<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Service\AuthorizationService;

class SearchCasesNameFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SearchCasesName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchCasesName
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $authService = $container->get(AuthorizationService::class);
        $urlHelper = $container->get('Helper\Url');
        return new SearchCasesName($authService, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SearchCasesCaseId
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchCasesName
    {
        return $this->__invoke($serviceLocator, SearchCasesName::class);
    }
}

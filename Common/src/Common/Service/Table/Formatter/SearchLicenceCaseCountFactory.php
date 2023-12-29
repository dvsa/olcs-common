<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;

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
        $authService = $container->get(AuthorizationService::class);
        return new SearchLicenceCaseCount($authService);
    }
}

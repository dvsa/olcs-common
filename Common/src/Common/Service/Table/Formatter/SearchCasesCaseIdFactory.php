<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;

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
        $authService = $container->get(AuthorizationService::class);
        return new SearchCasesCaseId($authService);
    }
}

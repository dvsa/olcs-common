<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;

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
        $authService = $container->get(AuthorizationService::class);
        $urlHelper = $container->get('Helper\Url');
        return new SearchPeopleRecord($authService, $urlHelper);
    }
}

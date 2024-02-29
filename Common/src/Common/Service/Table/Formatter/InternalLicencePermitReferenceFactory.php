<?php

namespace Common\Service\Table\Formatter;

use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcRbacMvc\Service\AuthorizationService;
use Psr\Container\ContainerInterface;

class InternalLicencePermitReferenceFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return InternalLicencePermitReference
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): InternalLicencePermitReference
    {
        $urlHelper = $container->get('Helper\Url');
        $authService = $container->get(AuthorizationService::class);
        return new InternalLicencePermitReference($urlHelper, $authService);
    }
}

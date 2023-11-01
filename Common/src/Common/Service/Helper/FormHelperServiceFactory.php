<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * FormHelperServiceFactory
 */
class FormHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return FormHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormHelperService
    {
        return new FormHelperService(
            $container->get('FormAnnotationBuilder'),
            $container->get('Config'),
            $container->get(AuthorizationService::class),
            $container->get('ViewRenderer'),
            $container->get('Data\Address'),
            $container->get('Helper\Address'),
            $container->get('Helper\Date'),
            $container->get('Helper\Translation')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return FormHelperService
     */
    public function createService(ServiceLocatorInterface $services): FormHelperService
    {
        return $this($services, FormHelperService::class);
    }
}

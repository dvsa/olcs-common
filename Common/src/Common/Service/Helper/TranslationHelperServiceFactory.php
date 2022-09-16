<?php

namespace Common\Service\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * TranslationHelperServiceFactory
 */
class TranslationHelperServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return TranslationHelperService
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslationHelperService
    {
        return new TranslationHelperService(
            $container->get('translator')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return TranslationHelperService
     */
    public function createService(ServiceLocatorInterface $services): TranslationHelperService
    {
        return $this($services, TranslationHelperService::class);
    }
}

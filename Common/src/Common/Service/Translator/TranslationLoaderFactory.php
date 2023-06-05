<?php

namespace Common\Service\Translator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the translation loader service (front end nodes)
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TranslationLoaderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslationLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslationLoader
    {
        return $this($serviceLocator, TranslationLoader::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return TranslationLoader
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslationLoader
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        return new TranslationLoader($container->get('QueryService'));
    }
}

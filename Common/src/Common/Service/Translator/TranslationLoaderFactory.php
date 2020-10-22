<?php

namespace Common\Service\Translator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $parentLocator = $container->getServiceLocator();

        return new TranslationLoader(
            $parentLocator->get('QueryService'),
            $parentLocator->get('TransferAnnotationBuilder')
        );
    }
}

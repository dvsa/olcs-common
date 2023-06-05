<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

class LanguageLinkFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return LanguageLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LanguageLink
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $languagePref = $container->get('LanguagePreference');

        return new LanguageLink($languagePref);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return LanguageLink
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): LanguageLink
    {
        return $this->__invoke($serviceLocator, null);
    }
}

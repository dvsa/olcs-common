<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

class TranslateReplaceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return TranslateReplace
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): TranslateReplace
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $translator = $container->get('Helper\Translation');

        return new TranslateReplace($translator);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TranslateReplace
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TranslateReplace
    {
        return $this->__invoke($serviceLocator, null);
    }
}

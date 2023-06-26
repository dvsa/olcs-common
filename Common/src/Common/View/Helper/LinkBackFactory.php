<?php

namespace Common\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use RuntimeException;

class LinkBackFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     *
     * @return LinkBack
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LinkBack
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $request = $container->get('Request');

        return new LinkBack($request);
    }

    /**
     * @deprecated can be removed following laminas v3 upgrade
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return LinkBack
     * @throws RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator): LinkBack
    {
        return $this->__invoke($serviceLocator, null);
    }
}

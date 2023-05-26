<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoteUrlFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return NoteUrl
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $request = $container->get('request');
        $urlHelper = $container->get('Helper\Url');
        return new NoteUrl($request, $urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return NoteUrl
     */
    public function createService(ServiceLocatorInterface $serviceLocator): NoteUrl
    {
        return $this->__invoke($serviceLocator, NoteUrl::class);
    }
}

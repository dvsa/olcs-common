<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TmApplicationManagerTypeFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return TmApplicationManagerType
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $application = $container->get('Application');
        $urlHelper = $container->get('Helper\Url');
        $translator = $container->get('translator');
        return new TmApplicationManagerType($application, $urlHelper, $translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TmApplicationManagerType
     */
    public function createService(ServiceLocatorInterface $serviceLocator): TmApplicationManagerType
    {
        return $this->__invoke($serviceLocator, TmApplicationManagerType::class);
    }
}

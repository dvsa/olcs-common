<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DataRetentionRecordLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return DataRetentionRecordLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        $viewHelperManager = $container->get('ViewHelperManager');
        return new DataRetentionRecordLink($urlHelper, $viewHelperManager);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DataRetentionRecordLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DataRetentionRecordLink
    {
        return $this->__invoke($serviceLocator, DataRetentionRecordLink::class);
    }
}

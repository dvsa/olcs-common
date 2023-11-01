<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class RefDataStatusFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return RefDataStatus
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $refDataFormatter = $container->get(FormatterPluginManager::class)->get(RefData::class);
        $viewHelperManager = $container->get('ViewHelperManager');
        return new RefDataStatus($viewHelperManager, $refDataFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RefDataStatus
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RefDataStatus
    {
        return $this->__invoke($serviceLocator, RefDataStatus::class);
    }
}

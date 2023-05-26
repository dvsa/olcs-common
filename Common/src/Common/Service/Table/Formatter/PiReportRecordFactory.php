<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class PiReportRecordFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return PiReportRecord
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $urlHelper = $container->get('Helper\Url');
        return new PiReportRecord($urlHelper);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PiReportRecord
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PiReportRecord
    {
        return $this->__invoke($serviceLocator, PiReportRecord::class);
    }
}

<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PiReportNameFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return PiReportName
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $organisationLink = $formatterPluginManager->get(OrganisationLink::class);
        $nameFormatter = $formatterPluginManager->get(Name::class);
        return new PiReportName($organisationLink, $nameFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PiReportName
     */
    public function createService(ServiceLocatorInterface $serviceLocator): PiReportName
    {
        return $this->__invoke($serviceLocator, PiReportName::class);
    }
}

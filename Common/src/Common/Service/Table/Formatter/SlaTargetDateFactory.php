<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SlaTargetDateFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return SlaTargetDate
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dateFormatter = $container->get(Date::class);
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $router = $container->get('router');
        $request = $container->get('request');
        $urlHelper = $container->get('Helper\Url');
        return new SlaTargetDate($router, $request, $urlHelper, $dateFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SlaTargetDate
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SlaTargetDate
    {
        return $this->__invoke($serviceLocator, SlaTargetDate::class);
    }
}

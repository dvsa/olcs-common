<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ConditionsUndertakingsTypeFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return ConditionsUndertakingsType
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        return new ConditionsUndertakingsType($translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConditionsUndertakingsType
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ConditionsUndertakingsType
    {
        return $this->__invoke($serviceLocator, ConditionsUndertakingsType::class);
    }
}

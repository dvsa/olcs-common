<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class EventHistoryUserFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return EventHistoryUser
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $translator = $container->get('translator');
        return new EventHistoryUser($translator);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EventHistoryUser
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EventHistoryUser
    {
        return $this->__invoke($serviceLocator, EventHistoryUser::class);
    }
}

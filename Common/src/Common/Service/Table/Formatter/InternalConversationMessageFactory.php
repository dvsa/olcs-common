<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InternalConversationMessageFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return InternalConversationMessage
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $formatterPluginManager = $container->get(FormatterPluginManager::class);

        return new InternalConversationMessage();
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InternalConversationMessage
     */
    public function createService(ServiceLocatorInterface $serviceLocator): InternalConversationMessage
    {
        return $this->__invoke($serviceLocator, InternalConversationMessage::class);
    }
}

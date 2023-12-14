<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\Service\Table\Formatter\InternalConversationMessage;

class InternalConversationMessageFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return ConversationMessage
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
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

<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ConversationMessageFactory implements FactoryInterface
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
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $refDataStatusFormatter = $formatterPluginManager->get(RefDataStatus::class);
        return new ConversationMessage($refDataStatusFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConversationMessage
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ConversationMessage
    {
        return $this->__invoke($serviceLocator, ConversationMessage::class);
    }
}

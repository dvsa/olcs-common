<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Common\Service\Table\Formatter\ConversationMessage;

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
        $urlHelper = $container->get('Helper\Url');
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

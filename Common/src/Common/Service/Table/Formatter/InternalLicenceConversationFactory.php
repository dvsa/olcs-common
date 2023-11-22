<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class InternalLicenceConversationFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return InternalLicenceConversationLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container = method_exists($container, 'getServiceLocator') ? $container->getServiceLocator() : $container;
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $refDataStatusFormatter = $formatterPluginManager->get(RefDataStatus::class);
       // $dateTimeFormatter = $formatterPluginManager->get(DateTime::class);
        $urlHelper = $container->get('Helper\Url');
        return new InternalLicenceConversationLink($urlHelper,$refDataStatusFormatter);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return InternalLicenceConversationLink
     */
    public function createService(ServiceLocatorInterface $serviceLocator): InternalLicenceConversationLink
    {
        return $this->__invoke($serviceLocator, InternalLicenceConversationLink::class);
    }
}

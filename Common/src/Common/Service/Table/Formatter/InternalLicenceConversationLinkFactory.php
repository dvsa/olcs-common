<?php

namespace Common\Service\Table\Formatter;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class InternalLicenceConversationLinkFactory implements FactoryInterface
{
    /**
     * @param  ContainerInterface $container
     * @param  $requestedName
     * @param  array|null         $options
     * @return InternalLicenceConversationLink
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formatterPluginManager = $container->get(FormatterPluginManager::class);
        $refDataStatusFormatter = $formatterPluginManager->get(RefDataStatus::class);
        $urlHelper = $container->get('Helper\Url');
        return new InternalLicenceConversationLink($urlHelper, $refDataStatusFormatter);
    }
}

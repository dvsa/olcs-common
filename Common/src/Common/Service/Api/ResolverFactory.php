<?php

namespace Common\Service\Api;

use Interop\Container\Containerinterface;
use Laminas\Mvc\Service\AbstractPluginManagerFactory;

class ResolverFactory extends AbstractPluginManagerFactory
{
    public const PLUGIN_MANAGER_CLASS = 'Common\Service\Api\Resolver';

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('Config');

        return parent::__invoke($container, $name, $config['rest_services']);
    }
}

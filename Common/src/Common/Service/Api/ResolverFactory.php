<?php

namespace Common\Service\Api;

use Laminas\Mvc\Service\AbstractPluginManagerFactory;
use Psr\Container\ContainerInterface;

class ResolverFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = Resolver::class;

    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $config = $container->get('Config');

        return parent::__invoke($container, $name, $config['rest_services']);
    }
}

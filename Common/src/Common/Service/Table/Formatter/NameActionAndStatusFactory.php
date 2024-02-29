<?php

namespace Common\Service\Table\Formatter;

use LmcRbacMvc\Service\AuthorizationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class NameActionAndStatusFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new NameActionAndStatus($container->get(AuthorizationService::class));
    }
}

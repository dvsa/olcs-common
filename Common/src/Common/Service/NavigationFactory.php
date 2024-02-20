<?php

namespace Common\Service;

use Psr\Container\ContainerInterface;
use Laminas\Navigation\Navigation;
use Laminas\Navigation\Service\ConstructedNavigationFactory;

class NavigationFactory
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getNavigation(array $config): Navigation
    {
        $factory = new ConstructedNavigationFactory($config);
        return $factory->__invoke($this->container, Navigation::class);
    }
}

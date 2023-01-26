<?php

namespace CommonTest\Controller\Lva\Stubs;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ControllerWithFactoryStub implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator): ControllerWithFactoryStub
    {
        return $this->__invoke($serviceLocator, ControllerWithFactoryStub::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ControllerWithFactoryStub
    {
        return $this;
    }
}

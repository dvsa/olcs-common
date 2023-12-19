<?php

namespace CommonTest\Common\Util\Stub;

use Interop\Container\ContainerInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ServiceWithFactoryStub implements \Laminas\ServiceManager\FactoryInterface
{
    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator): ServiceWithFactoryStub
    {
        return $this->__invoke($serviceLocator, ServiceWithFactoryStub::class);
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ServiceWithFactoryStub
    {
        return $this;
    }
}

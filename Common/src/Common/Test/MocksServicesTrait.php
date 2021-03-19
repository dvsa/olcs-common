<?php

namespace Common\Test;

use Common\Test\Builder\ServiceManagerBuilder;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery\MockInterface;
use Mockery as m;

trait MocksServicesTrait
{
    /**
     * @return ServiceLocatorInterface
     */
    protected function setUpServiceLocator(): ServiceLocatorInterface
    {
        return (new ServiceManagerBuilder(function (ServiceLocatorInterface $serviceLocator) {
            return $this->setUpDefaultServices($serviceLocator);
        }))->build();
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return MockInterface|AbstractPluginManager
     */
    protected function setUpAbstractPluginManager(ServiceLocatorInterface $serviceLocator): MockInterface
    {
        $instance = m::mock(AbstractPluginManager::class);
        $instance->shouldReceive('getServiceLocator')->andReturn($serviceLocator)->byDefault();
        return $instance;
    }

    /**
     * @param string $class
     * @return MockInterface
     */
    protected function setUpMockService(string $class): MockInterface
    {
        $instance = m::mock($class);
        $instance->shouldIgnoreMissing();
        return $instance;
    }

    /**
     * Sets up default services.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    abstract protected function setUpDefaultServices(ServiceLocatorInterface $serviceLocator): array;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @return MockInterface
     */
    protected function resolveMockService(ServiceLocatorInterface $serviceLocator, string $name): MockInterface
    {
        return $serviceLocator->get($name);
    }
}

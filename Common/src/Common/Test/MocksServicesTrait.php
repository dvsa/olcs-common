<?php

namespace Common\Test;

use Common\Test\Builder\ServiceManagerBuilder;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Mockery\MockInterface;
use Mockery as m;

trait MocksServicesTrait
{
    /**
     * @return ServiceManager
     */
    protected function setUpServiceLocator(): ServiceManager
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
     * @param ServiceManager $serviceManager
     */
    abstract protected function setUpDefaultServices(ServiceManager $serviceManager);

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

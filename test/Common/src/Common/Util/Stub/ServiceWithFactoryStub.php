<?php

namespace CommonTest\Util\Stub;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ServiceWithFactoryStub implements \Laminas\ServiceManager\FactoryInterface
{
    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }
}

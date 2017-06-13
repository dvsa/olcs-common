<?php

namespace CommonTest\Util\Stub;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ServiceWithFactoryStub implements \Zend\ServiceManager\FactoryInterface
{
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }
}

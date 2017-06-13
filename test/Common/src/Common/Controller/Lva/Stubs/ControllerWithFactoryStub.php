<?php

namespace CommonTest\Controller\Lva\Stubs;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ControllerWithFactoryStub implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }
}

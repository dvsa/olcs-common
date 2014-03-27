<?php

namespace Common\Service\Api;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\Api\RestClientStub;

class RestApiResolver implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this;
    }
    
    public function getClient($endpoint) {
        return new RestClientStub($endpoint);
    }
}

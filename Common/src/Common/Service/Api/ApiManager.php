<?php

namespace Common\Service\Api;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ApiManager implements FactoryInterface {
    private $routeParams;
    private $httpRequest;
    private $restClient;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mvcEvent = $serviceLocator->get('Application')->getMvcEvent();
        
        $this->httpRequest = $mvcEvent->getRequest();
        $this->routeParams = $mvcEvent->getRouteMatch()->getParams();
        $this->restClient  = $serviceLocator->get('ServiceApiResolver')->getClient($myClass);
        
        return $this;
        
    }

    public function hydrate() {
        
    }
}
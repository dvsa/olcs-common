<?php

namespace Common\Service\Api;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Service\Model\FlowModelHydrator;

class ModelRestClient implements FactoryInterface {
    private $hydrator;
    private $restResolver;
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->setHydrator($serviceLocator->get('Hydrator'))
                    ->setRestResolver($serviceLocator->get('RestApiResolver'));
    }
    
    public function setHydrator(FlowModelHydrator $hydrator) {
        $this->hydrator = $hydrator;
        return $this;
    }
    
    public function setRestResolver($restResolver) {
        $this->restResolver = $restResolver;
        return $this;
    }
    
    public function create($model, $path = null)
    {
        $restClient = $this->restResolver->getClient(get_class($model));
        return $restClient->create($this->hydrator->extract($model), $path);
    }
    
    public function get($model) 
    {
        $restClient = $this->restResolver->getClient(get_class($model));
        return $this->hydrator->setData($restClient->get())->hydrate($model);
    }
}
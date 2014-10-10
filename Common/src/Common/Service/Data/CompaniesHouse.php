<?php

namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\FactoryInterface;
use Common\Util\RestCallTrait;

class CompaniesHouse implements FactoryInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait,
        RestCallTrait;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);

        return $this;
    }

    public function search($type, $value)
    {
        return $this->makeRestCall(
            'CompaniesHouse',
            'GET',
            [
                'type' => $type,
                'value' => $value
            ]
        );
    }
}

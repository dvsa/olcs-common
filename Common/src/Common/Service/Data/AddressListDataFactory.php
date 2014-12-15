<?php

namespace Common\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AddressListDataFactory
 * @package Common\Service\Data
 */
class AddressListDataFactory implements FactoryInterface
{

    /**
     * Factory method to create the required service depending on type of address required
     * @param ServiceLocatorInterface $serviceLocator
     * @return AddressListDataService|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new AddressListDataService();

        return $service;
    }
}

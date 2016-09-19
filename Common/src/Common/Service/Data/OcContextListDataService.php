<?php

namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\FactoryInterface;

/**
 * Class OcContextListDataService
 * @package Olcs\Service
 */
class OcContextListDataService implements FactoryInterface, ListDataInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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

    /**
     * Calls either the LicenceOperatingCentre List data service or  the ApplicationOperatingCentre list data service
     * to return a list of OCs associated with either the licence or application
     *
     * @param null $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        if ($context == 'licence') {
            return $this->getServiceLocator('DataServiceManager')
                ->get('Common\Service\Data\LicenceOperatingCentre')->fetchListOptions($context, $useGroups);
        } elseif ($context == 'application') {
            return $this->getServiceLocator('DataServiceManager')
                ->get('Common\Service\Data\ApplicationOperatingCentre')->fetchListOptions($context, $useGroups);
        }

        return [];
    }
}

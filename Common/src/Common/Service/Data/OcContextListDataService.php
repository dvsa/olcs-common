<?php

namespace Common\Service\Data;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class OcContextListDataService
 *
 * @package Olcs\Service\Data
 */
class OcContextListDataService implements FactoryInterface, ListDataInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return $this
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
     * @param array|string $context   Context
     * @param bool         $useGroups Use groups
     *
     * @return array
     */
    public function fetchListOptions($context, $useGroups = false)
    {
        if ($context == 'licence') {
            return $this->getServiceLocator('DataServiceManager')
                ->get('Common\Service\Data\LicenceOperatingCentre')
                ->fetchListOptions($context, $useGroups);
        } elseif ($context == 'application') {
            return $this->getServiceLocator('DataServiceManager')
                ->get('Common\Service\Data\ApplicationOperatingCentre')
                ->fetchListOptions($context, $useGroups);
        }

        return [];
    }
}

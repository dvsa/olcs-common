<?php

/**
 * Entity Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Processing;

use Common\Service\Data\CategoryDataService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Entity Processing Service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class EntityProcessingService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private $serviceMap = [
        CategoryDataService::CATEGORY_APPLICATION       => 'Licence',
        CategoryDataService::CATEGORY_BUS_REGISTRATION  => 'BusReg',
        CategoryDataService::CATEGORY_COMPLIANCE        => 'Cases',
        CategoryDataService::CATEGORY_LICENSING         => 'Licence',
        CategoryDataService::CATEGORY_ENVIRONMENTAL     => 'Licence',
        CategoryDataService::CATEGORY_IRFO              => 'Organisation',
        CategoryDataService::CATEGORY_TRANSPORT_MANAGER => 'TransportManager'
    ];

    private $descriptionMap = [
        CategoryDataService::CATEGORY_APPLICATION       => 'Licence',
        CategoryDataService::CATEGORY_BUS_REGISTRATION  => 'Bus route',
        CategoryDataService::CATEGORY_COMPLIANCE        => 'Case',
        CategoryDataService::CATEGORY_LICENSING         => 'Licence',
        CategoryDataService::CATEGORY_ENVIRONMENTAL     => 'Licence',
        CategoryDataService::CATEGORY_IRFO              => 'IRFO',
        CategoryDataService::CATEGORY_TRANSPORT_MANAGER => 'Transport manager'
    ];

    public function findEntityForCategory($category, $identifier)
    {
        return $this->getServiceLocator()
            ->get('Entity\\' . $this->serviceMap[$category])
            ->findByIdentifier($identifier);
    }

    public function findEntityNameForCategory($category)
    {
        return $this->descriptionMap[$category];
    }
}

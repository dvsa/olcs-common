<?php

namespace Common\Service\Data;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserTypesListDataService
 * Provides list options for user types
 *
 * @package Olcs\Service
 */
class UserTypesListDataService implements FactoryInterface, ListDataInterface
{

    /**
     * RefData Service
     * @var string
     */
    protected $refDataService;

    /**
     * Filters out all options but those allowable / implemented
     *
     * @param null $context
     * @param bool $useGroups
     * @return array
     */
    public function fetchListOptions($context = null, $useGroups = false)
    {
        $options = [
            'internal' => 'Internal',
            'transport-manager' => 'Transport manager',
            'partner' => 'Partner',
            'local-authority' => 'Local authority',
            'self-service' => 'Self service',
            'self-service-no-licence' => 'Self service no licence',
        ];
        return $options;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setRefDataService($serviceLocator->get('\Common\Service\Data\RefData'));

        return $this;
    }

    /**
     * @param string $refDataService
     */
    public function setRefDataService($refDataService)
    {
        $this->refDataService = $refDataService;
    }

    /**
     * @return string
     */
    public function getRefDataService()
    {
        return $this->refDataService;
    }
}

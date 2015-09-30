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
        return [
            'internal' => 'Internal',
            'local-authority' => 'Local authority',
            'operator' => 'Operator',
            'partner' => 'Partner',
            'transport-manager' => 'Transport manager / Operator with TM access',
        ];
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
